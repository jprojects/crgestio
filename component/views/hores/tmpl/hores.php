<?php
/**
 * @version     1.0.0 Afi Framework $
 * @package     Afi Framework
 * @copyright   Copyright © 2014 - All rights reserved.
 * @license	    GNU/GPL
 * @author	    kim
 * @author mail kim@afi.cat
 * @website	    http://www.afi.cat
 *
*/

defined('_Afi') or die ('restricted access');

$model 	= $app->getModel('hores');
$page  	= $app->getVar('page', 1, 'get');
$view  	= $app->getVar('view', '', 'get');
$tab  	= $app->getVar('tab', 'diari', 'get');
$date  	= $app->getVar('date', date('Y-m-d'), 'get');
?>

<style>
    .bg-day { cursor: pointer; }
    .bg-saturday { background-color: yellow; }
    .bg-sunday { background-color: red; }
    .bg-festiu {background-color: red;}
    .bg-baixa {background-color: black;}
    .bg-vacança {background-color: black;}

    .bg-actual {background-color: #ccc;}

    #tableau thead, #tableau tbody, #tableau tfoot, #tableau tr, #tableau td, #tableau th{
        border-style: inherit;
        border-width: 1px;
    }

</style>
    
    
<script langage="JavaScript">
    
</script>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Hores <?= $date != '' ? $date : ''; ?></h3>
                <p class="text-subtitle text-muted">Registre horari</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Inici</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hores</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

	<!-- Basic Horizontal form layout section start -->
    <section id="basic-horizontal-layouts">
        <div class="row match-height">
            <div class="col-md-4 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Calendari</h4>
                            <?= $html->renderButtons('hores', 'hores'); ?>
                        </div>
                        <span class="square green"></span> Vacançes
                        <span class="square black"></span> Baixes
                        <span class="square red"></span> Festius 
                        <span class="square yellow"></span> Dissabtes 
                        <span class="square blue1"></span><!----><span class="square blue2"><!----></span><!----><span class="square blue3"></span> Treball
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <!-- <div id="calendari" data-IdCalendari="<?= $user->IdCalendari; ?>"></div> -->
                            <div class="text-right" ID=tableau>
                                <form name="Calendrier">
                                <table style='font-size:9' class="table">
                                <tr class="text-center">
                                    <td>
                                    <div id=SelMois>
                                    <select name="Mois" id="mes" tabindex="1" onchange="Mode(1)" class="form-select" >
                                    </select>
                                    </div>
                                    </td>
                                    <td>
                                    <select name="Annee" id="any" tabindex="2" onchange="Mode(1)" class="form-select">
                                    </select>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td colspan="2">
                                    <div class="text-center" ID=Cal>
                                    </div>
                                    </td>
                                </tr>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Registre horari</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form-signin needs-validation" id="hores-form" action='<?= $config->site; ?>/index.php?view=hores&amp;task=saveHorari' method="post" novalidate>
                                <input type="hidden" name="IdTreballador" value="<?= $user->Id; ?>">
                                <input type="hidden" name="Data1" value="<?= $date; ?>">
                                <?= $html->getRepeatable('hores', array('Id', 'Hora', 'TipusMoviment', 'Comentari'), $model->getEntradesTreballador($date), null, null, null); ?>  
                                <button type="submit" class="btn btn-primary"><?= $lang->get('CW_SAVE'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

	<section class="section">
        <div class="card">
            <div class="card-body">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button onclick="document.getElementById('tab').value='diari';" class="nav-link <?php if($tab == 'diari') : ?>active<?php endif; ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#diari" type="button" role="tab" aria-controls="diari" aria-selected="true">Diari treball</button>
                    </li>
                    <!--
                    <li class="nav-item" role="presentation">
                        <button onclick="document.getElementById('tab').value='resum';"  class="nav-link <?php if($tab == 'resum') : ?>active<?php endif; ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#resum" type="button" role="tab" aria-controls="resum" aria-selected="false">Resum dies anteriors</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button onclick="document.getElementById('tab').value='vacances';"  class="nav-link <?php if($tab == 'vacances') : ?>active<?php endif; ?>" id="contact-tab" data-bs-toggle="tab" data-bs-target="#vacances" type="button" role="tab" aria-controls="vacances" aria-selected="false">Vacances 2021</button>
                    </li>
                        -->
                </ul>

                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="diari" role="tabpanel" aria-labelledby="diari-tab">
                        <form action="<?= $config->site; ?>/index.php?view=hores&task=saveRegistre" method="post" id="itemsList" name="itemsList" class="mt-3 w-100 needs-validation" novalidate>
                            <input type="hidden" name="IdTreballador" value="<?= $user->Id; ?>">
                            <input type="hidden" name="Data" value="<?= $date; ?>">
                            <input type="hidden" name="view" value="hores">
                            <input type="hidden" name="tab" id="tab" value="diari">
                            <?= $html->getRepeatable('horesPersonal', array('Id', 'IdProjecte', 'razonsocial', 'Hores', 'totalhoresrecompte', 'horesprevistes', 'KM', 'Dietes', 'Observacions'), $model->getRegistresTreballador($date), null, null, null); ?> 
                            <button type="submit" class="btn btn-primary"><?= $lang->get('CW_SAVE'); ?></button>
                        </form>
                    </div>
                    <!--
                    <div class="tab-pane fade" id="resum" role="tabpanel" aria-labelledby="resum-tab">..</div>
                    <div class="tab-pane fade" id="vacances" role="tabpanel" aria-labelledby="vacances-tab">...</div>
                    -->

                </div>

            </div>
        </div>

    </section>
</div>