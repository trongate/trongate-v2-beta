<section class="force_tall">
    <div class="container">
        <h1>Offer Accepted</h1>
        <p class="text-center">Thank you for accepting the offer.</p>
        <?php
        $btn_txt = 'View Winning Bidder\'s Details';
        ?>
        <p class="text-center neon_glow">
            <?= anchor('module_requests/view_request/'.segment(3), $btn_txt, array('class' => 'button alt')) ?>    
        </p>
    </div>
</section>