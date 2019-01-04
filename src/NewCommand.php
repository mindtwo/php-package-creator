<?php

namespace mindtwo\PhpPackageCreator;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class NewCommand extends Command
{
    protected $required_props = [
        'author_name',
        'author_github_username',
        'author_email',
        'package_name',
        'package_description',
    ];

    protected $directory;
    protected $author_name;
    protected $author_github_username;
    protected $author_email;
    protected $author_twitter;
    protected $author_website;
    protected $package_vendor;
    protected $package_name;
    protected $package_description;
    protected $package_folder;
    protected $psr4_namespace;

    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Crafting a new PHP package.')
            ->addArgument('package_folder', InputArgument::REQUIRED, $this->label('package_folder'))
            ->addOption('author_name', null, InputOption::VALUE_NONE, $this->label('author_name'), null)
            ->addOption('author_github_username', null, InputOption::VALUE_NONE, $this->label('author_github_username'), null)
            ->addOption('author_email', null, InputOption::VALUE_NONE, $this->label('author_email'), null)
            ->addOption('author_twitter', null, InputOption::VALUE_NONE, $this->label('author_twitter'), null)
            ->addOption('author_website', null, InputOption::VALUE_NONE, $this->label('author_website'), null)
            ->addOption('package_vendor', null, InputOption::VALUE_NONE, $this->label('package_vendor'), null)
            ->addOption('package_name', null, InputOption::VALUE_NONE, $this->label('package_name'), null)
            ->addOption('package_description', null, InputOption::VALUE_NONE, $this->label('package_description'), null)
            ->addOption('psr4_namespace', null, InputOption::VALUE_NONE, $this->label('psr4_namespace'), null)
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forces install even if the directory already exists', null);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initProps($input, $output);

        $output->writeln('<info>Crafting PHP Package...</info>');

        if (! $input->getOption('force')) {
            $this->verifyDirectoryDoesntExist();
        }

        $this->removeExistingDirectory($input, $output, $input->getOption('force'))
             ->copyProjectStub($input, $output)
             ->setReplacements($input, $output)
             ->cleanup($input, $output);

        $output->writeln('<comment>PHP Package is ready! Build something amazing.</comment>');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initProps(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->labels() as $property => $label) {
            $shellInput = array_merge($input->getArguments(), $input->getOptions());

            $this->$property = $shellInput[$property] ?: $this->ask($input, $output, $label, $property);
        }

        if ($input->getArgument('package_folder')) {
            $this->directory = getcwd().'/'.$input->getArgument('package_folder');
        } else {
            $this->directory = getcwd().'/'.($this->package_vendor ?? 'package_vendor').'-'.($this->package_name ?? 'package_name');
        }
    }

    /**
     * @return array
     */
    protected function labels(): array
    {
        return [
            'package_folder'         => 'Package folder name?',
            'author_name'            => 'Your name?',
            'author_github_username' => 'Your Github username?',
            'author_email'           => 'Your email address?',
            'author_twitter'         => 'Your twitter username? (Default: @{author_github_username})',
            'author_website'         => 'Your website? (Default: https://github.com/{author_github_username})',
            'package_vendor'         => 'Package vendor? (Default: {author_github_username})',
            'package_name'           => 'Package name?',
            'package_description'    => 'Package very short description?',
            'psr4_namespace'         => 'PSR-4 namespace? (Default: {package_vendor}\\{package_name})',
        ];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function label(string $key): string
    {
        if (array_key_exists($key, $this->labels())) {
            return $this->labels()[$key];
        }

        return '';
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param string $directory
     */
    protected function verifyDirectoryDoesntExist($directory = null)
    {
        $directory = $directory ?? $this->directory;

        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Folder already exists!');
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function copyProjectStub(InputInterface $input, OutputInterface $output): self
    {
        $output->writeln('<info>Copy skeleton files...</info>');

        $filesystem = new Filesystem();

        $filesystem->mirror(__DIR__.'/skeleton', $this->directory);

        $output->writeln('<info>Skeleton files are now in place!</info>');

        return $this;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param bool            $force
     *
     * @return $this
     */
    protected function removeExistingDirectory(InputInterface $input, OutputInterface $output, bool $force): self
    {
        $filesystem = new Filesystem();

        if ($force && $filesystem->exists($this->directory)) {
            $output->writeln('<info>Deleting old directory...</info>');

            $filesystem->remove($this->directory);

            $output->writeln('<info>Old directory was deleted!</info>');
        }

        return $this;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function setReplacements(InputInterface $input, OutputInterface $output)
    {
        $values = [];

        $replacements = [
            ':vendor\\\\:package_name\\\\' => function () use (&$values) {return str_replace('\\', '\\\\', $this->psr4_namespace).'\\\\'; },
            ':author_name'                 => function () use (&$values) {return $this->author_name; },
            ':author_username'             => function () use (&$values) {return $this->author_github_username; },
            ':author_website'              => function () use (&$values) {return $this->author_website ?: ('https://github.com/'.$this->author_github_username); },
            ':author_email'                => function () use (&$values) {return $this->author_email ?: ($this->author_github_username.'@example.com'); },
            ':vendor'                      => function () use (&$values) {return $this->package_vendor; },
            ':package_name'                => function () use (&$values) {return $this->package_name; },
            ':package_description'         => function () use (&$values) {return $this->package_description; },
            'League\\Skeleton'             => function () use (&$values) {return $this->psr4_namespace; },
        ];

        foreach (array_merge(
            glob($this->directory.'/*.md'),
            glob($this->directory.'/*.xml.dist'),
            glob($this->directory.'/composer.json'),
            glob($this->directory.'/src/*.php'),
            glob($this->directory.'/tests/*.php')
        ) as $f) {
            $contents = file_get_contents($f);
            foreach ($replacements as $str => $func) {
                $contents = str_replace($str, $func(), $contents);
                $contents = preg_replace('/\n\*\*Note:\*\*\sReplace.*\n/', '', $contents);
            }
            file_put_contents($f, $contents);
        }

        return $this;
    }

    /**
     * Remove some files from skeleton package.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function cleanup(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();

        $filesystem->remove($this->directory.'/prefill.php');
        $filesystem->remove($this->directory.'/.git');

        return $this;
    }

    /**
     * Ask for user command line input.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $label
     * @param string          $property
     *
     * @return string
     */
    protected function ask(InputInterface $input, OutputInterface $output, $label, $property): string
    {
        $helper = $this->getHelper('question');

        $question = (new Question($label, false))->setValidator(
            function ($value) use ($property) {
                if (empty(trim($value)) && in_array($property, $this->required_props)) {
                    throw new Exception("The $property cannot be empty.");
                }

                return $value;
            }
        );

        return $this->interpolate($helper->ask($input, $output, $question));
    }

    /**
     * Interpolate previously defined properties.
     *
     * @param string $text
     *
     * @return string
     */
    protected function interpolate(string $text): string
    {
        if (! preg_match_all('/\{(\w+)\}/', $text, $m)) {
            return $text;
        }
        foreach ($m[0] as $k => $str) {
            $f = $m[1][$k];
            $text = str_replace($str, $this->$f, $text);
        }

        return $text;
    }
}
