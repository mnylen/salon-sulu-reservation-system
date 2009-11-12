<?php $activeTab = "home"; include 'templates/admin/header.php'; ?>
    <h2>Login</h2>

    <?php if ($errorMessage) { ?>
        <p class="error"><?=$errorMessage;?></p>
    <?php } ?>

    <form action="index.php" method="post">
        <dl>
			<dt><label for="id_username">Username:</label></dt>
            <dd><input type="text" name="username" id="id_username" value="<?= htmlspecialchars($form->getData('username')); ?>" />
				<?php display_errors($form, 'username'); ?></dd>

        	<dt><label for="id_password">Password:</label></dt>
            <dd><input type="password" name="password" id="id_password" value="<?= htmlspecialchars($form->getData('password')); ?>" />
            	<?php display_errors($form, 'password'); ?></dd>

        	<dt><input type="submit" name="submit" value="Login" /></dt>
		</dl>
    </form>
<?php include 'templates/admin/footer.php'; ?>
