<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\config\driver;

class Xml
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function parse()
    {
        if (is_file($this->config)) {
            $content = simplexml_load_file($this->config);
        } else {
            $content = simplexml_load<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\config\driver;

class Xml
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function parse()
    {
        if (is_file($this->config)) {
            $content = simplexml_load_file($this->config);
        } else {
            $content = simplexml_load_string($this->config);
        }

        $result = (array) $content;
        foreach ($result as $key => $val) {
            if (is_object($val)) {
                $result[$key] = (array) $val;
            }
        }

        return $result;
    }
}
