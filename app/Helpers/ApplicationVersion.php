<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Helpers\Util\QuickGit;
use Config;

class ApplicationVersion
{
    private $versionController;

    public $cachePrefix = 'app_version_';
    public $cacheLifetime = 30; // minutes
    public $appType = 'api';

    public function __construct()
    {
        $this->versionController = new QuickGit;
    }

    /**
     * Get Application Version
     *
     * @param string $appType
     * @return void
     */
    public function getApplicationVersion($appType = 'api')
    {
        // Set App Type
        $this->setAppType($appType);

        $version = $this->loadApplicationVersion();

        return $version;
    }

    /**
     * Load Applicatin Version
     *
     * @return string
     */
    public function loadApplicationVersion()
    {
        try
        {
            $cachedVersionOption = Cache::has($this->getCacheKey());
            $cachedVersion       = Cache::get($this->getCacheKey());
            $re = ($cachedVersionOption && !empty($cachedVersion)) ? Cache::get($this->getCacheKey()) : $this->determineApplicationVersion();
        }
        catch (\Exception $e)
        {
            \Log::info([
                "Failed to get {$this->appType} Application Version",
                $e->getMessage()
            ]);

            $re = Config::get('app_settings.application.'.$this->appType.'.version');
        }

        return $re;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function determineApplicationVersion()
    {
        chdir(Config::get('app_settings.application.'.$this->appType.'.abs_path'));

        $version = new QuickGit();
        $version = $version->toString();

        Cache::put($this->getCacheKey(), $version, $this->cacheLifetime);

        return $version;
    }

    /**
     * Set App Type attribute.
     *
     * @param string $appType
     * @return string
     */
    public function setAppType($appType)
    {
        $this->appType = !empty($appType) ? strtolower($appType) : $this->appType;
    }

    /**
     * Get App Type attribute.
     *
     * @return string
     */
    public function getAppType()
    {
        $this->appType;
    }

    /**
     * Get Cache key
     *
     * @return void
     */
    public function getCacheKey()
    {
        return $this->cachePrefix.$this->appType;
    }
}