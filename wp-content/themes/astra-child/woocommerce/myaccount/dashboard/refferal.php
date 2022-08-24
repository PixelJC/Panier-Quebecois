<div class="dashboard-referral">
    <div class="mto-dashboard-container">
        <div class="dashboard-ref-img" style="background-image: url(https://panierquebecois.test/wp-content/uploads/2022/08/dashboard-referral.jpg);">
            <!-- <img src="https://panierquebecois.test/wp-content/uploads/2022/08/dashboard-referral.jpg" alt="Dashboard Referral" /> -->
        </div>
        <div class="dashboard-ref-content">
            <p class="dashboard-ref-title"><?php echo __('Refer a friend and recieve $20 each', 'panierquebecois'); ?></p>
            <p class="dashboard-ref-text"><?php echo __('Send the link below to your friends and recieve $20 for each refferred friend! They will also get a $20 discount on their first order.', 'panierquebecois'); ?></p>
            <!-- <a href="<?php echo home_url(); ?>?pqc=<?php echo $user_id ?>";><?php echo home_url(); ?>?pqc=<?php echo $user_id ?></a> -->
            <div class="dashboard-ref-btn">
                <a data-clipboard="<?php echo home_url(); ?>?pqc=<?php echo $user_id ?>" href="#";>
                    <img src="https://panierquebecois.test/wp-content/uploads/2022/08/copy-icon.png" alt="Copy Icon" />
                    <span><?php echo __('Copy url', 'panierquebecois'); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
