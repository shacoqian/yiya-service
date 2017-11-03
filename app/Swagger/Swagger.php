<?php
namespace App\Swagger;

/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/6/20
 * Time: 下午5:37
 */

class Swagger {

    //根命名空间
    public static $rootNameSpace = '';
    public static $swagger = [
        'swagger' => '2.0',
        'produces' => [
            'application/json'
        ],
        'schemes' => [
            'http'
        ]
    ];
    public static $disabled = [];

    public static $pre_url = '';
    private static $paths = [];
    private static $methods = ['get', 'post', 'put', 'delete', 'patch'];
    private static $variable_type = [
        'integer',
        'string',
        'array',
        'mexed',
        'boolean',
        'double',
        'object',
        'resource'
    ];

    //类列表
    private static $classes = [];

    /**
     * @param $path 生成文档的文件夹绝对路径
     */
    public static function load($path) {
        if(! is_dir($path)) {
            return false;
        }
        self::getNameSpaceList($path);
        self::getMulityDocument();
    }


    //遍历文件夹生成文件列表及命名空间
    private static function getNameSpaceList($path, $middleDir = '') {
        if ($handle = opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                        $itemMiddleDir = '';
                        if ($middleDir) {
                            $itemMiddleDir = $middleDir . '\\' . $file;
                        } else {
                            $itemMiddleDir = $file;
                        }
                        self::getNameSpaceList($path . DIRECTORY_SEPARATOR . $file, $itemMiddleDir);
                    } else {
                        $fileInfo = explode('.', $file);
                        if (count($fileInfo) == 2 && $fileInfo[1] == 'php') {
                            if (! in_array($fileInfo[0], self::$disabled)) {
                                self::$classes[] = [self::$rootNameSpace . '\\'
                                    . ( $middleDir ? $middleDir . '\\' : '' )
                                    . $fileInfo[0],
                                    ($middleDir ? $middleDir : 0)];
                            }
                        }
                    }
                }
            }
        }
    }

    //批量获取类中的注释和方法名
    private static function getMulityDocument() {
        foreach(self::$classes as $class) {
            if (class_exists($class[0])) {
                self::getDocuments($class[0], $class[1]);
            }
        }
        echo json_encode(self::$swagger);
    }

    //只获取类中有注释的方法和注释
    private static function getDocuments($class, $url_pre = '') {
        $obj = new \ReflectionClass($class);
        $methods = $obj->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classDoc = $obj->getDocComment();
        $tags = '未分类';
        if ($classDoc) {
            $classDoc = self::parse($classDoc);
            $tags = isset($classDoc['tags']) ? trim($classDoc['tags']) : $tags;
        }

        $className = $obj->getShortName();
        foreach($methods as $key => $action) {
            $actionName = $action->name;
            //过滤掉trait
            if ('\\' . $action->class == $class) {
                //过滤掉魔术方法
                if (substr($actionName, 0, 2) != '__') {
                    $document = $obj->getMethod($actionName)->getDocComment();
                    //过滤掉没写注释的方法
                    if ($document) {

                        $docData = self::parse($document);
                        $url = isset($docData['path']) ? $docData['path'] : '';
                        if (isset($docData['info'])) {
                            self::$swagger['info'] = $docData['info'];
                        } else {
                            $method = isset($docData['method']) ? $docData['method'] : 'get';
                            $summary = isset($docData['summary']) ? $docData['summary'] : '';
                            $description = isset($docData['description']) ? $docData['description'] : $summary;
                            $summary = $summary ? $summary : $description;
                            self::$swagger['paths'][$url] = [
                                $method => [
                                    'tags' => [(isset($docData['tags']) ? $docData['tags'] : $tags)],
                                    'summary' => $summary,
                                    'description' => $description,
                                    'produces' => ['application/json'],
                                    'parameters' => isset($docData['parameters']) ? $docData['parameters'] : [],
                                    'responses' => [
                                        '200' => (object)[
                                            'description' => '返回值',
                                            'schema' => [
                                                'properties' => [
                                                    'example' => [
                                                        'type' => 'object',
                                                        'default' => isset($docData['return']) ?  (json_decode($docData['return'], true) === null ? [] : json_decode($docData['return'], true)) : []
                                                    ]
                                                ]
                                            ]                                        ],
                                        '401' => (object)[],
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
    }

    private static function parse($docs) {
        $docs = preg_replace('/\/\*/', '' , $docs);
        $docs = preg_replace('/\*\//', '' , $docs);
        $docs = trim(preg_replace('/\*/', '' , $docs));
        $docs = explode(PHP_EOL, $docs);
        $docs = self::compatibelLineFeed($docs);
        $docData = [];
        foreach($docs as $doc) {
            $docArray = explode(' ', trim($doc));
            switch (trim($docArray[0])) {
                case '@description':
                    unset($docArray[0]);
                    $docData['description'] = implode(' ', $docArray);
                    break;
                case '@desc':
                    unset($docArray[0]);
                    $docData['description'] = implode(' ', $docArray);
                    break;
                case '@method':
                    $method = isset($docArray[1]) ? strtolower(trim($docArray[1])) : 'get';
                    if (! in_array($method, self::$methods)) {
                        $method = 'get';
                    }
                    $docData['method'] = $method;
                    break;
                case '@param':
                    unset($docArray[0]);
                    $variable = '';
                    foreach($docArray as $k => $v) {
                        if (strpos($v, '$') !== false) {
                            $variable = trim($v);
                            unset($docArray[$k]);
                            break;
                        }
                    }
                    if ($variable) {
                        $type = 'string';
                        if (isset($docArray[1]) && in_array(strtolower(trim($docArray[1])), self::$variable_type)) {
                            $type = trim($docArray[1]);
                        }
                        $docData['parameters'][] = [
                            'name' => str_replace('$', '', $variable),
                            'in' => isset($docArray[4]) ? $docArray[4] : 'query',
                            'type' => $type,
                            'description' => implode(' ', $docArray),
                            'default' => ''
                        ];
                    }
                    break;
                case '@info':
                    if(count($docArray) == 3) {
                        $docData['info'][trim($docArray[1])] = trim($docArray[2]);
                    }
                    break;
                case '@tags':
                    $docData['tags'] = isset($docArray[1]) ? trim($docArray[1]) : '';
                    break;
                case '@name':
                    $docData['summary'] = isset($docArray[1]) ? $docArray[1] : '';
                case '@path':
                    $docData['path'] = isset($docArray[1]) ? $docArray[1] : '';
                    break;
                case '@return':
                    $docData['return'] = isset($docArray[1]) ? str_replace('null', '""', $docArray[1]) : [];
                    break;
                default:
                    break;
            }
        }


        if (isset($docData['method']) && (strtolower($docData['method']) == 'post' || strtolower($docData['method']) == 'put') ) {
            $parameters = [];
            $body = [];
            foreach($docData['parameters'] as $k => $v) {
                if ($v['in'] == 'query') {
                    $body[$v['name']] = $v;
                } else {
                    $parameters[] = $v;
                }

            }
            if (! empty($body)) {
                $schema = [
                    'type' => 'object',
                    'properties' => []
                ];

                foreach($body as $k => $v) {
                    $schema['properties'] = array_merge(
                        $schema['properties'],
                        [
                            $k => [
                                'type' => $v['type'],
                                'default' => $v['description']
                            ]
                        ]
                    );
                }
                $parameters[] = [
                    'name' => 'body',
                    'in' => 'body',
                    'type' => $type,
                    'description' => '',
                    'schema' => $schema
                ];
            }
            $docData['parameters'] = $parameters;
        }


        return $docData;
    }

    //兼容换行
    private static function compatibelLineFeed($docs) {
        $newData = [];
        foreach($docs as $k => $doc) {
            if (strpos($doc, '@') === false) {
                if (isset($newData[$k-1])) {
                    $newData[$k-1] .= ' ' . trim($doc);
                }
            } else {
                $newData[$k] = $doc;
            }
        }
        return $newData;
    }
}