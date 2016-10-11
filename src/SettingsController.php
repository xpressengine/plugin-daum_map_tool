<?php
/**
 * Created by PhpStorm.
 * User: seungman
 * Date: 2016. 9. 22.
 * Time: 오후 5:35
 */

namespace Xpressengine\Plugins\DaumMapTool;

use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Permission\PermissionSupport;
use XePresenter;
use XeConfig;

class SettingsController extends Controller
{
    use PermissionSupport;

    public function getSetting($instanceId)
    {
        $permArgs = $this->getPermArguments(DaumMapTool::getKey($instanceId), 'use');

        return XePresenter::make('daum_map_tool::views.setting', [
            'instanceId' => $instanceId,
            'permArgs' => $permArgs
        ]);
    }

    public function postSetting(Request $request, $instanceId)
    {
        $this->permissionRegister($request, DaumMapTool::getKey($instanceId), 'use');

        return redirect()->route('settings.plugin.daum_map_tool.setting', $instanceId);
    }

    public function getGlobal()
    {
        $config = XeConfig::getOrNew('daum_map_tool');

        return XePresenter::make('daum_map_tool::views.global', ['config' => $config]);
    }

    public function postGlobal(Request $request)
    {
        XeConfig::set('daum_map_tool', $request->only(['key', 'lat', 'lng', 'zoom']));

        return redirect()->back();
    }
}