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
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class LineController extends Controller
{
    protected $bot;
    public function __construct()
    {
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
    }
    protected function welcome(string $reply_token): void
    {
        $base_size = new BaseSizeBuilder(1040, 1040);

        $area = new AreaBuilder(407, 518, 611, 244);

        $image_map_actions = [];
        $image_map_actions[] = new ImagemapMessageActionBuilder('看有何課程', $area);
        $image_map = new ImagemapMessageBuilder('https://storage.googleapis.com/dev-cdn.leadercampus.com.tw/line-bot/welcome', '歡迎進入創新學院', $base_size, $image_map_actions);
        $this->bot->replyMessage($reply_token, $image_map);
    }
    protected function myCourses(string $reply_token)
    {
        $actions = array(
            //一般訊息型 action
            new MessageTemplateActionBuilder("所有已購買課程", "所有已購買課程"),
            new MessageTemplateActionBuilder("未學習完成的課程", "未學習完成的課程"),
            new MessageTemplateActionBuilder("已學習完成的課程", "已學習完成的課程"),

            //網址型 action
            //new UriTemplateActionBuilder("Google", "http://www.google.com"),
            //下列兩筆均為互動型action
            // new PostbackTemplateActionBuilder("下一頁", "page=3"),
            // new PostbackTemplateActionBuilder("上一頁", "page=1")
        );
        $img_url = null;
        //   $img_url = "圖片網址，必需為 https (圖片非必填欄位)";
        $button = new ButtonTemplateBuilder("我的課程", "這裡會列出您已購買課程的項目", $img_url, $actions);
        $msg = new TemplateMessageBuilder("這訊息要用手機才看的到哦", $button);
        $this->bot->replyMessage($reply_token, $msg);
    }
    public function index(Request $request)
    {

        Log::info(print_r($request->all(), true));
        $input = $request->all();
        $events = collect($input['events']);

        $events->each(function ($event) use ($bot) {
            $response = [

            ];
            $reply_token = $event['replyToken'];
            $text = $event['message']['text'];
            switch ($text) {
                case '我的課程':
                    $this->myCourses($reply_token);
                    break;
                default:
                    $this->welcome($reply_token);
                    break;
            }
        });

        return 'this is line bot';
    }
}
