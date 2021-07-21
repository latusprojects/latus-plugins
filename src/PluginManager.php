<?php


namespace Latus\Plugins;


use Composer\Console\Application;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Latus\Helpers\Paths;
use Latus\Plugins\Models\ComposerRepository;
use Latus\Plugins\Models\Plugin;
use Latus\Plugins\Repositories\Contracts\PluginRepository;
use Symfony\Component\Console\Input\StringInput;

class PluginManager
{

    protected static Application|null $composer = null;

    public function __construct(
        protected Plugin|null $plugin
    )
    {
    }

    protected static function composer(): Application
    {
        $composer = new Application();
        $composer->setAutoExit(false);

        return $composer;
    }

    protected static function buildComposerJson()
    {
        $composer_path = Paths::pluginPath('composer.json');

        $data = json_decode(File::get($composer_path));

        $plugin_repository = App::make(PluginRepository::class, [new Plugin()]);

        /**
         * @var Collection $plugins
         */
        $plugins = $plugin_repository->getAllActive();

        $data->require = json_decode('{}');

        /**
         * @var Plugin $plugin
         */
        if ($plugins->isNotEmpty()) {
            foreach ($plugins as $plugin) {
                $data->require->{$plugin->name} = $plugin->target_version;
            }
        }

        File::put($composer_path, json_encode($data));
    }

    /**
     * @throws Exception
     */
    protected static function addRepositoryToComposer(ComposerRepository $composerRepository)
    {
        $input = new StringInput('config' . ' repositories.' . $composerRepository->name . ' ' . $composerRepository->type . ' "' . $composerRepository->url . '"');

        self::composer()->run($input);
    }

    /**
     * @throws Exception
     */
    public static function updatePlugin(string $name)
    {

        $input = new StringInput('update' . ' "' . $name . '"' . ' --with-dependencies');
        self::composer()->run($input);
    }

    /**
     * @throws Exception
     */
    protected static function addMetapackageToComposer()
    {
        $input = new StringInput('require' . ' "latusprojects/local-installations:dev-master"' . '');

        self::composer()->run($input);
    }

    protected static function copyPluginComposer()
    {
        if (!File::exists(base_path('plugins'))) {
            File::makeDirectory(base_path('plugins'));
        }
        File::copy(__DIR__ . '/../plugins/composer.json.noload', base_path('plugins\composer.json'));

    }

    /**
     * @throws Exception
     */
    public static function install()
    {
        self::copyPluginComposer();
        self::buildComposerJson();
        self::addRepositoryToComposer(new ComposerRepository(['name' => 'latus-plugins', 'type' => 'path', 'url' => 'plugins']));
        self::addMetapackageToComposer();
        self::updatePlugin('latusprojects/local-installations');
    }
}