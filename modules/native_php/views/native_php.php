<div class="container">
    <h1 class="text-center">What Is Native PHP?</h1>
    <p class="sm mt-0 text-center">by David Connelly, Founder of the Trongate Framework</p>

    <p class="mt-4 lead">PHP began its life as a simple, elegant templating language - words from its creator, Rasmus Lerdorf. Yet today, many frameworks wrap PHP in layers of complexity: autoloaders, service containers, facades, and templating engines stacked on top of templating engines. Complexity has become the status quo. And that’s a problem.</p>

    <hr class="my-5">

    <h2>Why Most Frameworks Fail</h2>
    <p>Modern PHP frameworks promise productivity but deliver bureaucracy. They give us thousands of lines of boilerplate, sprawling <code>vendor/</code> directories, and rigid standards like PSR-4 mean developers spend more time managing code than writing it.  And oh... the constant rewrites!</p>
    <p>The worst part? Many of these “standards” are set by literal self-appointed governing bodies, like PHPFIG.</p>

    <p><b>I hereby accuse all self-appointed "PHP police" of gross incompetence and I want war ...with all of you.</b></p>

<div class="text-center mt-3 mb-2">
<figure class="image-container">
    <img src="images/war.png" alt="Beautiful mountain landscape at sunset">
    <figcaption><i>"I want war with all of you!"</i></figcaption>
</figure>    
</div>


<style>
.image-container {
    margin: 0 auto;
}

code pre {
  padding: 1em;
  color: #eee;
  background-color: #333;
  overflow: auto;
}
</style>
    



<p>The time has come to call out some of the downright stupid decisions that have been made by the afficionados that have ruined the PHP landscape. Take PSR-4 autoloading as an example: what should be a simple <code>require</code> or <code>include</code> has ballooned into hundreds of lines of code, multiple lookup paths, fallback directories, closures, and obscure logic.</p>

<p>Do you think I'm just ranting?  Read on!</p>
<hr>
<h2>The Standards Are Bullschitt</h2>

<p><b>Every PHP application that uses Composer for dependency management and PSR-4 autoloading ultimately relies on this very ClassLoader class, or a very close variant of it.</b></p>

<p>It's time to start scrolling...</p>

<code><pre>&lt;?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Autoload;

/**
 * ClassLoader implements a PSR-0, PSR-4 and classmap class loader.
 *
 *     $loader = new \Composer\Autoload\ClassLoader();
 *
 *     // register classes with namespaces
 *     $loader->add('Symfony\Component', __DIR__.'/component');
 *     $loader->add('Symfony',           __DIR__.'/framework');
 *
 *     // activate the autoloader
 *     $loader->register();
 *
 *     // to enable searching the include path (eg. for PEAR packages)
 *     $loader->setUseIncludePath(true);
 *
 * In this example, if you try to use a class in the Symfony\Component
 * namespace or one of its children (Symfony\Component\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 *
 * This class is loosely based on the Symfony UniversalClassLoader.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @see    https://www.php-fig.org/psr/psr-0/
 * @see    https://www.php-fig.org/psr/psr-4/
 */
class ClassLoader
{
    /** @var \Closure(string):void */
    private static $includeFile;

    /** @var string|null */
    private $vendorDir;

    // PSR-4
    /**
     * @var array<string, array<string, int>>
     */
    private $prefixLengthsPsr4 = array();
    /**
     * @var array<string, list<string>>
     */
    private $prefixDirsPsr4 = array();
    /**
     * @var list<string>
     */
    private $fallbackDirsPsr4 = array();

    // PSR-0
    /**
     * List of PSR-0 prefixes
     *
     * Structured as array('F (first letter)' => array('Foo\Bar (full prefix)' => array('path', 'path2')))
     *
     * @var array<string, array<string, list<string>>>
     */
    private $prefixesPsr0 = array();
    /**
     * @var list<string>
     */
    private $fallbackDirsPsr0 = array();

    /** @var bool */
    private $useIncludePath = false;

    /**
     * @var array<string, string>
     */
    private $classMap = array();

    /** @var bool */
    private $classMapAuthoritative = false;

    /**
     * @var array<string, bool>
     */
    private $missingClasses = array();

    /** @var string|null */
    private $apcuPrefix;

    /**
     * @var array<string, self>
     */
    private static $registeredLoaders = array();

    /**
     * @param string|null $vendorDir
     */
    public function __construct($vendorDir = null)
    {
        $this->vendorDir = $vendorDir;
        self::initializeIncludeClosure();
    }

    /**
     * @return array<string, list<string>>
     */
    public function getPrefixes()
    {
        if (!empty($this->prefixesPsr0)) {
            return call_user_func_array('array_merge', array_values($this->prefixesPsr0));
        }

        return array();
    }

    /**
     * @return array<string, list<string>>
     */
    public function getPrefixesPsr4()
    {
        return $this->prefixDirsPsr4;
    }

    /**
     * @return list<string>
     */
    public function getFallbackDirs()
    {
        return $this->fallbackDirsPsr0;
    }

    /**
     * @return list<string>
     */
    public function getFallbackDirsPsr4()
    {
        return $this->fallbackDirsPsr4;
    }

    /**
     * @return array<string, string> Array of classname => path
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * @param array<string, string> $classMap Class to filename map
     *
     * @return void
     */
    public function addClassMap(array $classMap)
    {
        if ($this->classMap) {
            $this->classMap = array_merge($this->classMap, $classMap);
        } else {
            $this->classMap = $classMap;
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string              $prefix  The prefix
     * @param list<string>|string $paths   The PSR-0 root directories
     * @param bool                $prepend Whether to prepend the directories
     *
     * @return void
     */
    public function add($prefix, $paths, $prepend = false)
    {
        $paths = (array) $paths;
        if (!$prefix) {
            if ($prepend) {
                $this->fallbackDirsPsr0 = array_merge(
                    $paths,
                    $this->fallbackDirsPsr0
                );
            } else {
                $this->fallbackDirsPsr0 = array_merge(
                    $this->fallbackDirsPsr0,
                    $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (!isset($this->prefixesPsr0[$first][$prefix])) {
            $this->prefixesPsr0[$first][$prefix] = $paths;

            return;
        }
        if ($prepend) {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                $paths,
                $this->prefixesPsr0[$first][$prefix]
            );
        } else {
            $this->prefixesPsr0[$first][$prefix] = array_merge(
                $this->prefixesPsr0[$first][$prefix],
                $paths
            );
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string              $prefix  The prefix/namespace, with trailing '\\'
     * @param list<string>|string $paths   The PSR-4 base directories
     * @param bool                $prepend Whether to prepend the directories
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        $paths = (array) $paths;
        if (!$prefix) {
            // Register directories for the root namespace.
            if ($prepend) {
                $this->fallbackDirsPsr4 = array_merge(
                    $paths,
                    $this->fallbackDirsPsr4
                );
            } else {
                $this->fallbackDirsPsr4 = array_merge(
                    $this->fallbackDirsPsr4,
                    $paths
                );
            }
        } elseif (!isset($this->prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = $paths;
        } elseif ($prepend) {
            // Prepend directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                $paths,
                $this->prefixDirsPsr4[$prefix]
            );
        } else {
            // Append directories for an already registered namespace.
            $this->prefixDirsPsr4[$prefix] = array_merge(
                $this->prefixDirsPsr4[$prefix],
                $paths
            );
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string              $prefix The prefix
     * @param list<string>|string $paths  The PSR-0 base directories
     *
     * @return void
     */
    public function set($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr0 = (array) $paths;
        } else {
            $this->prefixesPsr0[$prefix[0]][$prefix] = (array) $paths;
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace,
     * replacing any others previously set for this namespace.
     *
     * @param string              $prefix The prefix/namespace, with trailing '\\'
     * @param list<string>|string $paths  The PSR-4 base directories
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setPsr4($prefix, $paths)
    {
        if (!$prefix) {
            $this->fallbackDirsPsr4 = (array) $paths;
        } else {
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this->prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this->prefixDirsPsr4[$prefix] = (array) $paths;
        }
    }

    /**
     * Turns on searching the include path for class files.
     *
     * @param bool $useIncludePath
     *
     * @return void
     */
    public function setUseIncludePath($useIncludePath)
    {
        $this->useIncludePath = $useIncludePath;
    }

    /**
     * Can be used to check if the autoloader uses the include path to check
     * for classes.
     *
     * @return bool
     */
    public function getUseIncludePath()
    {
        return $this->useIncludePath;
    }

    /**
     * Turns off searching the prefix and fallback directories for classes
     * that have not been registered with the class map.
     *
     * @param bool $classMapAuthoritative
     *
     * @return void
     */
    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        $this->classMapAuthoritative = $classMapAuthoritative;
    }

    /**
     * Should class lookup fail if not found in the current class map?
     *
     * @return bool
     */
    public function isClassMapAuthoritative()
    {
        return $this->classMapAuthoritative;
    }

    /**
     * APCu prefix to use to cache found/not-found classes, if the extension is enabled.
     *
     * @param string|null $apcuPrefix
     *
     * @return void
     */
    public function setApcuPrefix($apcuPrefix)
    {
        $this->apcuPrefix = function_exists('apcu_fetch') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN) ? $apcuPrefix : null;
    }

    /**
     * The APCu prefix in use, or null if APCu caching is not enabled.
     *
     * @return string|null
     */
    public function getApcuPrefix()
    {
        return $this->apcuPrefix;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     *
     * @return void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);

        if (null === $this->vendorDir) {
            return;
        }

        if ($prepend) {
            self::$registeredLoaders = array($this->vendorDir => $this) + self::$registeredLoaders;
        } else {
            unset(self::$registeredLoaders[$this->vendorDir]);
            self::$registeredLoaders[$this->vendorDir] = $this;
        }
    }

    /**
     * Unregisters this instance as an autoloader.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));

        if (null !== $this->vendorDir) {
            unset(self::$registeredLoaders[$this->vendorDir]);
        }
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return true|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            $includeFile = self::$includeFile;
            $includeFile($file);

            return true;
        }

        return null;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }
        if ($this->classMapAuthoritative || isset($this->missingClasses[$class])) {
            return false;
        }
        if (null !== $this->apcuPrefix) {
            $file = apcu_fetch($this->apcuPrefix.$class, $hit);
            if ($hit) {
                return $file;
            }
        }

        $file = $this->findFileWithExtension($class, '.php');

        // Search for Hack files if we are running on HHVM
        if (false === $file && defined('HHVM_VERSION')) {
            $file = $this->findFileWithExtension($class, '.hh');
        }

        if (null !== $this->apcuPrefix) {
            apcu_add($this->apcuPrefix.$class, $file);
        }

        if (false === $file) {
            // Remember that this class does not exist.
            $this->missingClasses[$class] = true;
        }

        return $file;
    }

    /**
     * Returns the currently registered loaders keyed by their corresponding vendor directories.
     *
     * @return array<string, self>
     */
    public static function getRegisteredLoaders()
    {
        return self::$registeredLoaders;
    }

    /**
     * @param  string       $class
     * @param  string       $ext
     * @return string|false
     */
    private function findFileWithExtension($class, $ext)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;

        $first = $class[0];
        if (isset($this->prefixLengthsPsr4[$first])) {
            $subPath = $class;
            while (false !== $lastPos = strrpos($subPath, '\\')) {
                $subPath = substr($subPath, 0, $lastPos);
                $search = $subPath . '\\';
                if (isset($this->prefixDirsPsr4[$search])) {
                    $pathEnd = DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $lastPos + 1);
                    foreach ($this->prefixDirsPsr4[$search] as $dir) {
                        if (file_exists($file = $dir . $pathEnd)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($this->fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }

        // PSR-0 lookup
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                . strtr(substr($logicalPathPsr4, $pos + 1), '_', DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DIRECTORY_SEPARATOR) . $ext;
        }

        if (isset($this->prefixesPsr0[$first])) {
            foreach ($this->prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($this->fallbackDirsPsr0 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                return $file;
            }
        }

        // PSR-0 include paths.
        if ($this->useIncludePath && $file = stream_resolve_include_path($logicalPathPsr0)) {
            return $file;
        }

        return false;
    }

    /**
     * @return void
     */
    private static function initializeIncludeClosure()
    {
        if (self::$includeFile !== null) {
            return;
        }

        /**
         * Scope isolated include.
         *
         * Prevents access to $this/self from included files.
         *
         * @param  string $file
         * @return void
         */
        self::$includeFile = \Closure::bind(static function($file) {
            include $file;
        }, null, null);
    }
}</pre></code>

<p>Just look at all that bullschitt code.  Look at it!</p>

<p>Not only is it bloated, but somewhere along the way, the aficionados of “modern PHP” decided to abandon snake_case - the very style PHP was built on - for camelCase, creating a needless cognitive divide between language and convention.</p>

<div class="text-center mt-3 mb-2 container-xs" style="max-width: 360px;">
<figure class="image-container">
    <img src="images/old_lady.png" alt="Beautiful mountain landscape at sunset">
    <figcaption><i>"The dirty bastards ruined PHP!"</i></figcaption>
</figure>    
</div>

<p>Anyone out there who wants to say that I'm mad or who wants to accuse me of ranting will have to defend the code that I've just pasted in!</p>

<p>And yes... that's just the tip of the iceberg!</p>

<hr>
<h2>There Is Some Good News</h2>
<p>Please don't make the mistake of assuming that I don't like PHP, the language.</p>
<p>Everything I've said, above, pertains to decisions that have been made by literal self-appointed code police.  These are not the same people who are responsible for creating and/or looking after PHP - the language.</p>
<p>The good news is, PHP is still a great language.  Indeed, far from hating PHP, I want to encourage developers to return to the core language.  PHP is beautiful!</p>
<p>We need a better way to work with PHP - one that embraces the language, not hides it behind layers of needless abstraction.</p>

<h2>The Native PHP Philosophy</h2>
<p>Native PHP is a return to first principles. It’s built on three core values:</p>
<ul>
    <li><strong>Clarity:</strong> Code reads like English. You see what it does at a glance.</li>
    <li><strong>Speed:</strong> No overhead, no bloated abstractions - just fast execution.</li>
    <li><strong>Control:</strong> Every line is yours. No hidden dependencies, no surprises.</li>
</ul>
<p>This isn’t anti-progress - it’s anti-nonsense. Native PHP rejects unnecessary complexity while making full use of modern PHP’s power.</p>

<h2>The Birth of a Movement</h2>
<p>As Trongate evolved, developers asked: <em>“What do we call this?”</em> A framework that embraces PHP 8+, type hints, return types, RESTful APIs, yet refuses over-engineered standards had no name… until now.</p>
<p>The answer: <strong>Native PHP</strong>.</p>

<p>Wait a minute.  That's not the answer I was thinking of.  I mean, the <i>real</i> answer.  The answer that solves all the bullschitt.  Let's all say it together...</p>

<h2 class="text-center blink">Break the rules - use Trongate!</h2>

<hr class="my-5">

<h2>Trongate: Native PHP in Action</h2>
<p>Trongate is the first framework built entirely on Native PHP principles. A single 180 KB download contains everything you need: no <code>vendor/</code> folder, no lock file, no dependency hell.</p>
<p><strong>Benchmarks prove it:</strong> Trongate outperforms Laravel, Symfony, and CodeIgniter in load time, memory usage, and throughput. Why? Because we reject bloated, over-engineered code.</p>
<p>Developers using Trongate report:</p>
<ul>
    <li>50–70% fewer lines of code than Laravel or Symfony equivalents</li>
    <li>Sub-20 ms response times on shared hosting</li>
    <li>Zero-downtime upgrades in seconds, not hours</li>
</ul>

<div class="text-center mt-0 mb-2 container-xs" style="max-width: 720px;">
<figure class="image-container">
    <img src="images/group_laugh.png" alt="Beautiful mountain landscape at sunset">
    <figcaption><i>"We're mad as Hell and we're not gonna take it anymore!"</i></figcaption>
</figure>    
</div>

<h2>Enterprise Strength, Startup Agility</h2>
<p>Simplicity isn’t weakness. The same philosophy that powers Go on Wall Street and Rust at Amazon powers Trongate:</p>
<ul>
    <li>Single codebase, deployable on any PHP 8+ host</li>
    <li>Full type hints and return types throughout the core</li>
    <li>Battle-tested on sites handling millions of daily requests</li>
</ul>

<h2>The Trongate Coding Standard</h2>
<p>Every convention in Trongate maximises productivity and minimises cognitive load:</p>
<div class="row my-5">
    <div class="col-md-4">
        <h4>Pure PHP Syntax</h4>
        <p>No magic methods, no custom DSL - just PHP the way it was meant to be.</p>
    </div>
    <div class="col-md-4">
        <h4>Compact K&R Style</h4>
        <p>Clean, C-inspired formatting keeps logic visible and readable.</p>
    </div>
    <div class="col-md-4">
        <h4>Predictable Naming</h4>
        <p><code>snake_case</code> files, plural modules, zero namespaces - find anything in seconds.</p>
    </div>
</div>

<p>Example controller method:</p>
<code><pre>function create() {
    $data['form_location'] = current_url();
    $data['view_file'] = 'create';
    $this->template('public', $data);
}</pre></code>
<p>Four lines. Zero configuration. Instant clarity.</p>

<h2>Join the Native PHP Movement</h2>


<p>Thousands of developers are already embracing freedom from framework fatigue. They build faster, debug less, and sleep better knowing their applications won’t break when a dependency releases a patch at 3 a.m.</p>

<hr class="my-5">

<h2 class="text-center mb-0">Change has come to PHP</h2>


<p>So what are you waiting for? Tear off the shackles of bloated frameworks, cast aside the gatekeepers, and take back control of your code. Trongate isn’t just another framework - it’s a declaration of independence for developers who still believe that elegance, speed, and simplicity matter. The tools are ready, the movement has begun, and the future of PHP is once again in the hands of those bold enough to write it natively. Join us - and let’s build something extraordinary.</p>

<h2 class="text-center">Break the rules - use Trongate.</h2>
<p class="mb-3 text-center">Let them know that you disagree with their BULLSCHITT!  Send the bastards a message!  Give Trongate a star on GitHub!</p>

<p class="text-center mb-1"><?= anchor(GITHUB_URL, 'Give Trongate a Star on GitHub', array('class' => 'button mt-0')) ?></p>
</div>


<div class="text-center mt-0 mb-2 container-xs" style="max-width: 720px;">
<figure class="image-container">
    <img src="images/old_lady2.png" alt="Beautiful mountain landscape at sunset">
    <figcaption><i>"Send the bastards a message that they won't forget!"</i></figcaption>
</figure>    

<p class="text-center mb-1"><?= anchor(GITHUB_URL, 'Give Trongate a Star on GitHub', array('class' => 'button mt-0')) ?></p>
<h2 class="text-center mt-2 mb-5">Together we <i class="blink" style="color: black;">SHALL</i> make PHP great again!</h2>
</div>
