        <?php if (isset($breadcrumbs) && !empty($breadcrumbs)) : ?>
            <div class="breadcrumb-container">
                <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                        <?php $total = count($breadcrumbs); ?>
                        <?php foreach ($breadcrumbs as $index => $crumb) : ?>
                            <?php $is_last = ($index === $total - 1); ?>
                            <li <?= $is_last ? 'aria-current="page"' : '' ?>>
                                <?php if ($is_last): ?>
                                    </i><?= htmlspecialchars($crumb['title']) ?>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>