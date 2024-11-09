
<?php foreach($menus as $menu):?>
    <li class="nav-item">
        <a href="#" class="nav-link"> <i class="nav-icon <?= $menu['icone']?>"></i>
            <p>
                <?= $menu['categoria'] ?>
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>

        <ul class="nav nav-treeview">
            <?php foreach($menu['controllers'] as $m):?>
                <li class="nav-item">
                    <a href="<?= site_url($m["name"]) ?>" class="nav-link">
                        <i class="<?= $m["icone"] ?> nav-icon"></i>
                        <p><?= $m["titulo"] ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </li>
<?php endforeach; ?>



          