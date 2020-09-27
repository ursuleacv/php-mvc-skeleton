<?php $v->layout('layout', ['title' => 'Create User']) ?>

<div class="row justify-content-center">
        <div class="col-md-6 mt-2">
            <h2 class="font-weight-light text-center text-info">Create User</h2>

            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <div class="alert alert-danger"><?= $error[0] ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($result)) : ?>
                <div class="alert alert-success mt-4"> Success! </div>
            <?php endif; ?>

            <form method="post" class="mt-4 mb-5" action="/create">
                <div class="form-group">
                    <input class="form-control" type="text" name="first_name" placeholder="First Name"
                           value="<?=$data['first_name'] ?? '';?>">
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="last_name" placeholder="Last Name"
                           value="<?=$data['last_name'] ?? '';?>">
                </div>
                <div class="form-group">
                    <input class="form-control" type="email" name="email" placeholder="Email"
                           value="<?=$data['email'] ?? '';?>">
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </div>
                <input class="btn btn-block btn-primary btn-custom" type="submit" value="Save">
            </form>
        </div>
    </div>