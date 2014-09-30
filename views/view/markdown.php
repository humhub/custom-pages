
<div class="container">

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                $parser = new CMarkdownParser;
                echo $parser->safeTransform($md);
                ?>

            </div>
        </div>
    </div>
</div>