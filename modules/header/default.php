<?php

/**
 * @version     1.0.0 Afi framework $
 * @package     Afi framework
 * @copyright   Copyright © 2016 - All rights reserved.
 * @license	    GNU/GPL
 * @author	    kim
 * @author mail kim@afi.cat
 * @website	    http://www.afi.cat
 *
*/

defined('_Afi') or die ('restricted access');
$config     = factory::getConfig();
$user       = factory::getUser();
$settings   = factory::getSettings();
?>

<header>
    <nav class="navbar navbar-expand navbar-light ">
        <div class="container-fluid g-0">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php if($user->getAuth()) : ?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item me-3">
                        <div class="form-check form-switch">
                            <input class="mt-3 form-check-input" type="checkbox" id="darkmodeHeaderBtn" <?php if($settings->dark_mode == 1) : ?>checked<?php endif; ?>>
                            <label class="nav-link form-check-label" for="darkmode"><?php if($settings->dark_mode == 1) : ?><i class="bi bi-sun-fill bi-sub fs-4 text-gray-600"></i><?php else: ?><i class="bi bi-moon-fill bi-sub fs-4 text-gray-600"></i><?php endif; ?></label>
                        </div>
                    </li>
                    <?php if($settings->show_messages == 1) : ?>
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class='bi bi-bell bi-sub fs-4 text-gray-600'></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li>
                                <h6 class="dropdown-header">Notificacions</h6>
                            </li>
                            <li><a class="dropdown-item">No notification available</a></li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-menu d-flex">
                            <div class="user-name text-end me-3">
                                <h6 class="mb-0 nav-link text-gray-600"><?= $user->Treballadors; ?></h6>
                            </div>
                            <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md">
                                <img src="https://secure.gravatar.com/avatar/<?= md5($user->email); ?>?size=100">
                                </div>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="index.php?view=profile"><i class="icon-mid bi bi-person me-2"></i>
                                Perfil</a>
                        </li>
                        <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="index.php?task=register.logout"><i class="icon-mid bi bi-box-arrow-left me-2"></i> Sortir</a></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>
</header>
