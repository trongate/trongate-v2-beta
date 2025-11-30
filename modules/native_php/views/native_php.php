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
...about 500 lines of code, now reduced for brevity
</pre></code>

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
