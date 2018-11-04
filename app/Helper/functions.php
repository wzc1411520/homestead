<?php
/**
 * Created by PhpStorm.
 * User: wzc
 * Date: 2018/10/28
 * Time: 14:51
 */
namespace App\Helper;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class HelperController {
    protected static $allowed_ext = ["png", "jpg", "gif", 'jpeg'];


    //图片上传
    public static function save($file, $folder, $file_prefix, $max_width = false)
    {
        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());

        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        // 如果上传的不是图片将终止操作
        if ( ! in_array($extension,self::$allowed_ext)) {
            return false;
        }

        // 将图片移动到我们的目标存储路径中
        $file->move($upload_path, $filename);

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && in_array($extension,self::$allowed_ext)) {

            // 此类中封装的函数，用于裁剪图片
            self::reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => "/$folder_name/$filename"
        ];
    }

//裁剪图片
    public static function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null,function ($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }

//上传
    public function up_video(Request $request)
    {
        //获取文件信息
        $file = $request->file('Filedata');
        if ($file->isValid()&&strtolower($file->extension())=='mp4'){
            //附件上传
            $rst = $file->store('video','public');
            echo json_encode(['success'=>true,'filename'=>"/storage/".$rst]);
        }else{
            echo json_encode(['success'=>false,'info'=>'上传失败|仅支持MP4格式']);
        }
        exit();
    }
//上传文件
    public function up_pic(Request $request,$path='other',$max_width=false)
    {
        //获取文件信息
        $file = $request->file('Filedata');
        if ($file->isValid()){
            //附件上传
            $filePath = $path.'/'.date('Ymd');
            $rst = $file->store($filePath,'public');
            if ($max_width) {

                // 此类中封装的函数，用于裁剪图片
                $this->reduceSize("/storage/".$rst, $max_width);
            }
            echo json_encode(['success'=>true,'filename'=>"/storage/".$rst]);
        }else{
            echo json_encode(['success'=>false]);
        }
        exit();
    }
}
class SlugTranslateHandler
{
    public function translate($text)
    {
        // 实例化 HTTP 客户端
        $http = new Client();

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid. $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        // 发送 HTTP Get 请求
        $response = $http->get($api.$query);

        $result = json_decode($response->getBody(), true);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
        "from" => "zh"
        "to" => "en"
        "trans_result" => array:1 [▼
        0 => array:2 [▼
        "src" => "XSS 安全漏洞"
        "dst" => "XSS security vulnerability"
        ]
        ]
        ]

         **/

        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['trans_result'][0]['dst']);
        } else {
            // 如果百度翻译没有结果，使用拼音作为后备计划。
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}