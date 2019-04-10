<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineController extends Controller
{
    protected $bot;

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
    }

    protected function recommends(string $reply_token): void
    {
        $columns = [];

        $actions = [];
        // $actions[] = new PostbackTemplateActionBuilder('google', 'course_id=1');
        $actions[] = new UriTemplateActionBuilder("Google", "http://www.google.com");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/channel/201902/channel-5c6a55246f23c.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('1', '數位轉型關鍵對談', $image, $actions);

        $actions = [];
        // $actions[] = new PostbackTemplateActionBuilder('查看詳情', 'course_id=2');
        $actions[] = new UriTemplateActionBuilder("創新學院官網", "http://www.leadercampus.com.tw");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/article/201903/course-5c9c73495ccda.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('2', '學會學：學習之道', $image, $actions);

        $actions = [];
        // $actions[] = new PostbackTemplateActionBuilder('查看詳情', 'course_id=3');
        $actions[] = new UriTemplateActionBuilder("天下官網", "http://www.cw.com.tw");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/article/201901/course-5c3489c4ddcd5.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('3', '數位轉型從領導力開始', $image, $actions);

        $carousel = new CarouselTemplateBuilder($columns);
        $msg = new TemplateMessageBuilder('這訊息要用手機才看的到哦', $carousel);

        $this->bot->replyMessage($reply_token, $msg);
    }

    protected function welcome(string $reply_token): void
    {
        $base_size = new BaseSizeBuilder(1040, 1040);

        $area = new AreaBuilder(407, 518, 611, 244);

        $image_map_actions = [];
        $image_map_actions[] = new ImagemapMessageActionBuilder('help', $area);
        $image_map = new ImagemapMessageBuilder('https://storage.googleapis.com/dev-cdn.leadercampus.com.tw/line-bot/welcome/v1.0', '歡迎進入創新學院', $base_size, $image_map_actions);
        $this->bot->replyMessage($reply_token, $image_map);
    }

    protected function help(string $reply_token)
    {
        $actions = [
            //一般訊息型 action
            new MessageTemplateActionBuilder("歡迎", "welcome"),
            new MessageTemplateActionBuilder("我的課程", "我的課程"),
            new MessageTemplateActionBuilder("我的能力", "我的能力"),
            new MessageTemplateActionBuilder("推薦", "推薦"),
        ];
        $img_url = null;
        //   $img_url = "圖片網址，必需為 https (圖片非必填欄位)";
        $buttons = new ButtonTemplateBuilder("沃課功能", "這裡會列出沃課的功能", $img_url, $actions);
        $msg = new TemplateMessageBuilder("這訊息要用手機才看的到哦", $buttons);
        $this->bot->replyMessage($reply_token, $msg);
    }

    protected function myCourses(string $reply_token)
    {
        $actions = [
            //一般訊息型 action
            new MessageTemplateActionBuilder("所有已購買課程", "所有已購買課程"),
            new MessageTemplateActionBuilder("未學習完成的課程", "未學習完成的課程"),
            new MessageTemplateActionBuilder("已學習完成的課程", "已學習完成的課程"),

            //網址型 action
            //new UriTemplateActionBuilder("Google", "http://www.google.com"),
            //下列兩筆均為互動型action
            // new PostbackTemplateActionBuilder("下一頁", "page=3"),
            // new PostbackTemplateActionBuilder("上一頁", "page=1")
        ];
        $img_url = null;
        //   $img_url = "圖片網址，必需為 https (圖片非必填欄位)";
        $button = new ButtonTemplateBuilder("我的課程", "這裡會列出您已購買課程的項目", $img_url, $actions);
        $msg = new TemplateMessageBuilder("這訊息要用手機才看的到哦", $button);
        $this->bot->replyMessage($reply_token, $msg);
    }

    protected function finishedCourses(string $reply_token)
    {
        $columns = [];
        $actions = [];
        $actions[] = new UriTemplateActionBuilder("已學習完成的課程", "http://www.leadercampus.com.tw");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/channel/201902/channel-5c6a55246f23c.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('1', '數位轉型關鍵對談', $image, $actions);
        $carousel = new CarouselTemplateBuilder($columns);
        $msg = new TemplateMessageBuilder('這訊息要用手機才看的到哦', $carousel);

        $this->bot->replyMessage($reply_token, $msg);
    }

    protected function unfinishedCourses(string $reply_token)
    {
        $columns = [];
        $actions = [];
        $actions[] = new UriTemplateActionBuilder("未學習完成的課程", "http://www.leadercampus.com.tw");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/article/201903/course-5c9c73495ccda.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('1', '學會學：學習之道', $image, $actions);
        $carousel = new CarouselTemplateBuilder($columns);
        $msg = new TemplateMessageBuilder('這訊息要用手機才看的到哦', $carousel);

        $this->bot->replyMessage($reply_token, $msg);
    }

    protected function myAbility(string $reply_token)
    {
        $actions = [
            //一般訊息型 action
            new MessageTemplateActionBuilder("專屬推薦", "推薦"),
        ];
        $img_url = 'https://storage.googleapis.com/dev-cdn.leadercampus.com.tw/line-bot/ability.png';
        //   $img_url = "圖片網址，必需為 https (圖片非必填欄位)";
        $button = new ButtonTemplateBuilder("我的能力分布", "這是您的能力分布圖", $img_url, $actions);
        $msg = new TemplateMessageBuilder("這訊息要用手機才看的到哦", $button);
        $this->bot->replyMessage($reply_token, $msg);
    }
    protected function recentlyPurchasedCourses(string $reply_token)
    {
        $columns = [];
        $actions = [];
        $actions[] = new UriTemplateActionBuilder("所有已購買課程", "http://www.leadercampus.com.tw");
        $image = 'https://storage.googleapis.com/www-leadercampus-com-tw/leader/images/article/201901/course-5c3489c4ddcd5.jpg';
        $columns[] = new CarouselColumnTemplateBuilder('1', '數位轉型從領導力開始', $image, $actions);
        $carousel = new CarouselTemplateBuilder($columns);
        $msg = new TemplateMessageBuilder('這訊息要用手機才看的到哦', $carousel);

        $this->bot->replyMessage($reply_token, $msg);
    }

    public function index(Request $request)
    {

        Log::info(print_r($request->all(), true));
        $input = $request->all();
        $events = collect($input['events']);

        $events->each(function ($event) {
            $response = [

            ];
            $reply_token = $event['replyToken'];
            $text = $event['message']['text'];
            switch ($text) {
                case '我的課程':
                    $this->myCourses($reply_token);
                    break;
                case '推薦':
                    $this->recommends($reply_token);
                    break;
                case 'welcome':
                    $this->welcome($reply_token);
                    break;
                case '所有已購買課程':
                    $this->recentlyPurchasedCourses($reply_token);
                    break;
                case '未學習完成的課程':
                    $this->unfinishedCourses($reply_token);
                    break;
                case '已學習完成的課程':
                    $this->finishedCourses($reply_token);
                    break;
                case '我的能力':
                    $this->myAbility($reply_token);
                    break;
                default:
                    $this->help($reply_token);
                    break;
            }
        });

        return 'this is line bot';
    }
}
