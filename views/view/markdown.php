

<?php if ($navigationClass == CustomPage::NAV_CLASS_ACCOUNTNAV): ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            $parser = new CMarkdownParser;
            echo $parser->safeTransform($md);
            ?>

        </div>
    </div>

<?php else: ?>

    <div class="container">

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="custom_page_content">
                        <?php
                        $parser = new CMarkdownParser;
                        echo $parser->safeTransform($md);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>
