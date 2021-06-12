<?php
declare(strict_types=1);

namespace K3ssen\SymfonyVuetified\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class SymfonyVuetifiedSetupCommand extends Command
{
    protected static $defaultName = 'symfony-vuetified:setup';
    protected static $defaultDescription = 'Adds/modifies required files/settings for setup SymfonyVuetified';

    /**
     * @var string
     */
    protected $rootDir;

    public function __construct(string $rootDir)
    {
        parent::__construct(static::$defaultName);
        $this->rootDir = $rootDir;
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filesystem = new Filesystem();

        if (!$this->validateIfCommandShouldRun($io, $filesystem)) {
            return 1; // (Command::FAILURE  not supported in 4.4)
        }
        if ($input->isInteractive() && !$this->askContinue($io)) {
            return 1; // (Command::FAILURE  not supported in 4.4)
        }

        $this->modifyPackageJson($io, $filesystem);
        $this->appendTsConfigJs($io, $filesystem);
        $this->modifyWebpackConfig($io, $filesystem);
        $this->modifyAppJs($io, $filesystem);
        $this->modifyBaseHtmlTwig($io, $filesystem);
        $io->success('Done! You can now run build the assets, by running "yarn install & yarn dev" for example.');
        return 0; // (Command::SUCCESS  not supported in 4.4)
    }

    protected function appendTsConfigJs(SymfonyStyle $io, Filesystem $filesystem)
    {
        $io->comment('Trying to add tsconfig.json to root of project...');
        $filesystem->dumpFile($this->rootDir . DIRECTORY_SEPARATOR . 'tsconfig.json',
            file_get_contents(__DIR__
                . DIRECTORY_SEPARATOR
                . '..'
                . DIRECTORY_SEPARATOR
                . 'Resources'
                . DIRECTORY_SEPARATOR
                . 'assets'
                . DIRECTORY_SEPARATOR
                . 'tsconfig.json'
            )
        );
        $io->comment('tsconfig.json added to root of project');
    }

    protected function askContinue(SymfonyStyle $io)
    {
        $io->warning('This command will modify package.json, modify the webpack.config.js file, modify the assets/app.js file, replace templates/base.html.twig, and add the tsconfig.json file. It is strongly advised to commit any changes you\'ve made so far, so that you can evaluate the changes that are made after running this command.');
        return $io->askQuestion( new ConfirmationQuestion('Do you want to continue?', false));
    }

    protected function modifyPackageJson(SymfonyStyle $io, Filesystem $filesystem)
    {
        $packagePath = $this->rootDir . DIRECTORY_SEPARATOR . 'package.json';
        $packageJsonContent = file_get_contents($packagePath);
        if (stripos($packageJsonContent, '"@k3ssen/symfony-vuetified"') === false) {
            $io->comment('Trying to modify package.json ...');
            $packageJsonContent = str_replace(
                '"devDependencies": {',
                "\"devDependencies\": {\n        \"@k3ssen/symfony-vuetified\": \"file:vendor/k3ssen/symfony-vuetified/Resources/assets\",",
                $packageJsonContent
            );
            $filesystem->dumpFile($packagePath, $packageJsonContent);
            $io->comment('package.json modified');
        }
    }

    protected function modifyWebpackConfig(SymfonyStyle $io, Filesystem $filesystem)
    {
        $io->comment('Trying to modify webpack.config.js');
        $webpackFilePath = $this->rootDir . DIRECTORY_SEPARATOR . 'webpack.config.js';
        $webpackConfig = "const { VuetifyLoaderPlugin } = require('vuetify-loader');\n" . file_get_contents($webpackFilePath);
        $webpackConfig = str_replace('//.enableSassLoader()', '.enableSassLoader()', $webpackConfig);
        $webpackConfig = str_replace('//.enableTypeScriptLoader()', '.enableTypeScriptLoader()
    .configureLoaderRule(\'typescript\', rule => {
        delete rule.exclude;
    })
    .enableVueLoader(() => {
    }, {
        runtimeCompilerBuild: true,
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
', $webpackConfig);

        $filesystem->dumpFile($webpackFilePath, $webpackConfig);
        $io->comment('webpack.config.js modified');
    }

    protected function modifyAppJs(SymfonyStyle $io, Filesystem $filesystem)
    {
        $appJsPath = $this->rootDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'app.js';
        $io->comment('Trying to modify assets/app.js ...');
        $filesystem->appendToFile($appJsPath, "\n\nimport '@k3ssen/symfony-vuetified';");
        $io->comment('assets/app.js modified');
    }

    protected function modifyBaseHtmlTwig(SymfonyStyle $io, Filesystem $filesystem)
    {
        $io->comment('Trying to modify templates/base.html.twig ...');
        $baseHtmlTwig = $this->rootDir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'base.html.twig';
        $filesystem->dumpFile($baseHtmlTwig, "{% extends '@SymfonyVuetified/base.html.twig' %}\n");
        $io->comment('templates/base.html.twig modified');
    }

    protected function validateIfCommandShouldRun(SymfonyStyle $io, Filesystem $filesystem): bool
    {
        if (!$filesystem->exists($appJsPath = $this->rootDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'app.js')) {
            $io->error('File assets/app.js not found.');
            return false;
        }
        if (!$filesystem->exists($this->rootDir . DIRECTORY_SEPARATOR . 'webpack.config.js')) {
            $io->error('File webpack.config.js not found.');
            return false;
        }
        if ($filesystem->exists($this->rootDir . DIRECTORY_SEPARATOR . 'tsconfig.json')) {
            $io->error('This project already has a tsconfig.json file. Please configure SymfonyVuetify manually.');
            return false;
        }
        $webpackConfig = file_get_contents($this->rootDir . DIRECTORY_SEPARATOR . 'webpack.config.js');
        $webpackLineNotFoundError = 'To prevent this command from messing up your project, a line with "%s" is searched in your webpack.config.js, but no such line is found. This probably means you already made changes to your webpack.config.js or symfony-encore created a webpack.config.js with content that is not expected by this bundle. Please configure SymfonyVuetify manually.';
        if (stripos($webpackConfig, '//.enableSassLoader()') === false) {
            $io->error(sprintf($webpackLineNotFoundError, '//.enableSassLoader()'));
            return false;
        }
        if (stripos($webpackConfig, '//.enableTypeScriptLoader()') === false) {
            $io->error(sprintf($webpackLineNotFoundError, '//.enableTypeScriptLoader()'));
            return false;
        }

        if (stripos($webpackConfig, '    .enableSassLoader') !== false) {
            $io->error('It seems .enableSassLoader() is already uncommented in your webpack.config.js. 

                Please configure SymfonyVuetify manually.
            ');
            return false;
        }
        if (stripos($webpackConfig, '    .enableTypeScriptLoader') !== false) {
            $io->error('It seems .enableTypeScriptLoader() is already uncommented in your webpack.config.js. 

                Please configure SymfonyVuetify manually.
            ');
            return false;
        }
        if (stripos($webpackConfig, '    .enableVueLoader') !== false) {
            $io->error('It seems .enableVueLoader() is already applied in your webpack.config.js. 

                Please configure SymfonyVuetify manually.
            ');
            return false;
        }
        return true;
    }
}
