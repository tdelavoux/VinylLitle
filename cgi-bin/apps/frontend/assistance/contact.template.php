<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<?php $confirmation=\Form::getConfirms(); ?>
		<?php if(!empty($confirmation)): ?>
			<div class="alert alert-success alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<?php foreach($confirmation as $value): ?>
					<?php echo $value; ?>
				<?php endforeach; ?>
			</div>
		<?php endif;?>
		<?php if(\Page::get('gestionnaire')): ?>
			<?php $gestionnaire = \Page::get('gestionnaire'); ?>
			<?php $clickToTelGestionnaire = \Page::get('clickToTelGestionnaire'); ?>
		<?php endif; ?>
		<?php if(\Page::get('developpeur')): ?>
			<?php $developpeur = \Page::get('developpeur'); ?>
			<?php $clickToTelDeveloppeur = \Page::get('clickToTelDeveloppeur'); ?>
		<?php endif; ?>
		<table class="table table-bordered">
			<tbody>
				<tr class="head">
					<td colspan="2">Information de contact</td>
				</tr>
				<?php if($developpeur && $gestionnaire): ?>
				<tr>
					<td>Service technique / Gestionnaire :</td>
					<td><?php echo \Db::decode($developpeur['service']) . ' / ' . \Db::decode($gestionnaire['service']) ?></td>
				</tr>
				<tr>
					<td>Contact technique / Gestionnaire :</td>
					<td><a href="sip:<?php echo $developpeur['email']; ?>"><i class="fab fa-skype"></i> </a><?php echo \Db::decode($developpeur['nom']) . ' / '; ?><a href="sip:<?php echo $gestionnaire['email'] ?>"><i class="fab fa-skype"></i> </a><?php echo \Db::decode($gestionnaire['nom']) ?></td>
				</tr>
				<tr>
					<td>E-mail technique / Gestionnaire :</td>
					<td><?php echo $developpeur['email'] . ' / ' . $gestionnaire['email'] ?></td>
				</tr>
				<tr>
					<td>Téléphone technique / Gestionnaire :</td>
					<td>
						<a target="clickToCall"  href="<?php echo $clickToTelDeveloppeur ?>"><img src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>images/tel.png" class="image-tel"></a><?php echo $developpeur['telephone'] ?> /
						<a target="clickToCall" href="<?php echo $clickToTelGestionnaire ?>"><img src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>images/tel.png" class="image-tel"></a><?php echo $gestionnaire['telephone'] ?>
					</td>
				</tr>
				<?php else: ?>
				<tr>
					<td colspan="2">Aucune information de contact !</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php if($developpeur && $gestionnaire): ?>
			<button id="btnContact" class="btn btn-info">Nous contacter</button>
		<?php endif; ?>
	</div>
	<?php $errors=\Form::getErrors(); ?>
	<?php if($errors){ ?>
	<div class="col-md-8 col-md-offset-2" style="margin-top: 20px" id="contact">
	<?php }else{ ?>
	<div class="col-md-8 col-md-offset-2" style="display: none; margin-top: 20px" id="contact">
	<?php } ?>
		<form method="post" class="form-horizontal" action="<?php echo \Application::getRoute('assistance', 'envoi-message'); ?>" enctype="multipart/form-data">
		<table class="table table-bordered">
			<tbody>
				<tr class="head">
					<td colspan="2">Demande d'assistance</td>
				</tr>
				<tr>
					<td>Objet :</td>
					<td>
						<select class="form-control" name="objet">
							<option value="Assistance" <?php if(\Form::param('objet')=='Assistance'){ echo 'selected="selected"';}?>>Demande d'assistance sur l'outil <?php echo \config\Configuration::$vars['application']['name']; ?></option>
							<option value="Technique" <?php if(\Form::param('objet')=='Technique'){ echo 'selected="selected"';}?>>Problème technique sur l'outil <?php echo \config\Configuration::$vars['application']['name']; ?></option>
							<option value="Amelioration" <?php if(\Form::param('objet')=='Amelioration'){ echo 'selected="selected"';}?>>Je souhaite proposer une amélioration pour l'outil <?php echo \config\Configuration::$vars['application']['name']; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Message :</td>
					<td><textarea class="form-control message-area" id="message" name= "message" placeholder="Entrer votre message ici"><?php echo \Form::param('message'); ?></textarea></td>
				</tr>
				<tr>
					<td>Fichier :</td>
					<td><input type="file" id="fichier" name="fichier"/></td>
				</tr>
				<tr>
					<td colspan="2">Complément d'information : <b>Penser à ajouter une capture d'écran de votre problème ci-dessus</b> !</td>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($errors)):?>
			<div class="alert alert-danger alert-dismissable">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<?php foreach($errors as $value): ?>
					<?php echo $value; ?>
				<?php endforeach; ?>
			</div>
		<?php endif;?>
		<button id="btnEnvoie" class="btn btn-success">Envoyer</button>
		</form>
	</div>
</div>

<iframe style="display: none;" name="clickToCall"></iframe>

<script type="text/javascript">
	$('#btnContact').on('click',function(){
		if($('#contact').is(":visible"))
		{
			$('#contact').hide();
		}
		else
		{
			$('#contact').show();
		}
	  });
</script>