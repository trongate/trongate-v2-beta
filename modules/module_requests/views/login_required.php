<section class="force_tall">
    <div class="container">
        <h1>Login Required</h1>
        <p class="text-center"><?= $info ?></p>
        <p class="text-center neon_glow">
            <?php
            echo anchor('members/login', 'Login', array('class' => 'button'));
            echo anchor('members/join', 'Create Account', array('class' => 'button alt'));
            ?>
        </p>
        <p class="text-center"><?= anchor(previous_url(), 'Return To Previous Page') ?></p>
        <p class="text-center top_margin">Creating an account here is absolutely free and it only takes a few moments to join.</p>
    </div>
</section>