<?php

namespace Xpressengine\Plugins\DaumMapTool;
use App\Facades\XeFrontend;
use Illuminate\Contracts\Auth\Access\Gate;
use Symfony\Component\DomCrawler\Crawler;
use Xpressengine\Config\ConfigManager;
use Xpressengine\Editor\AbstractTool;
use Xpressengine\Permission\Instance;

class DaumMapTool extends AbstractTool
{
    protected $configs;

    protected $gate;

    public function __construct(ConfigManager $configs, Gate $gate, $instanceId)
    {
        parent::__construct($instanceId);

        $this->configs = $configs;
        $this->gate = $gate;
    }

    public function initAssets()
    {
        $config = $this->configs->getOrNew('daum_map_tool');

        XeFrontend::html('daum_map_tool.load_url')->content("
        <script>
            (function() {
            
                var _url = {
                    popup: '".route('daum_map_tool::popup')."',      
                    edit_popup: '".route('daum_map_tool::popup.edit')."'
                };
            
                var URL = {
                    get: function (type) {
                        return _url[type];                 
                    }
                };
                
                window.daumToolURL = URL;
            })();
        </script>
        ")->load();
        XeFrontend::js([
            '//apis.daum.net/maps/maps3.js?apikey=' . $config->get('key'),
            asset($this->getAssetsPath() . '/daumMapTool.js'),
            asset($this->getAssetsPath() . '/daumMapRenderer.js?key=' . $config->get('key'))
        ])->load();
    }

    public function getIcon()
    {
        return asset($this->getAssetsPath() . '/icon.png');
    }

    public static function getInstanceSettingURI($instanceId)
    {
        return null;
//        return route('settings.plugin.daum_map_tool.setting', $instanceId);
    }

    public static function getKey($instanceId)
    {
        return static::getId() .  '.' . $instanceId;
    }

    public function compile($content)
    {
        $config = $this->configs->getOrNew('daum_map_tool');

        XeFrontend::js([
            '//apis.daum.net/maps/maps3.js?apikey=' . $config->get('key'),
            asset($this->getAssetsPath() . '/daumMapRenderer.js?key=' . $config->get('key'))
        ])->load();

        $crawler = $this->createCrawler($content);
        $crawler->filter('*[xe-tool-id="editortool/daummap@daummap"]')->each(function (Crawler $node, $i) {
            $dom = $node->getNode(0);
            $script = $dom->ownerDocument->createElement('script');
            $txt = $dom->ownerDocument->createTextNode('$(function() { $("#' . $node->attr('id') . '").renderer(); })');
            $script->appendChild($txt);
            $dom->appendChild($script);

            $node->add($dom);
        });

        return $crawler->getNode(0)->ownerDocument->saveHTML($crawler->getNode(0));
        
    }
    private function getAssetsPath()
    {
        return str_replace(base_path(), '', realpath(__DIR__ . '/../assets'));
    }

    private function createCrawler($content)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        return $crawler;
    }
}