<div class="wrap">
    <h2>SD WP List Table</h2>
    <div id="container">
        <form method="post">
            <?php
            $this->sd_table->prepare_items();
            $this->sd_table->display(); 
            ?>
        </form>
    </div>
</div>