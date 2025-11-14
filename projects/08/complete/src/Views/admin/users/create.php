<?php $this->layout('layouts/main');
$this->start('content'); ?>
<section class="section">
    <div class="container">
        <h1 class="title">Create User</h1>
        <form class="box" method="post" action="/admin/users">
            <?php $this->csrfField(); ?>
            <div class="field"><label class="label" for="name">Name</label>
                <div class="control"><input class="input" id="name" type="text" name="name" required></div>
            </div>
            <div class="field"><label class="label" for="email">Email</label>
                <div class="control"><input class="input" id="email" type="email" name="email" required></div>
            </div>
            <div class="field"><label class="label" for="role">Role</label>
                <div class="control">
                    <div class="select"><select id="role" name="role">
                            <option value="user">user</option>
                            <option value="editor">editor</option>
                            <option value="admin">admin</option>
                        </select></div>
                </div>
            </div>
            <div class="field"><label class="label" for="password">Password</label>
                <div class="control"><input class="input" id="password" type="password" name="password" required></div>
            </div>
            <div class="field is-grouped">
                <div class="control"><button class="button is-primary" type="submit">Create</button></div>
                <div class="control"><a class="button" href="/admin/users">Cancel</a></div>
            </div>
        </form>
    </div>
</section>
<?php $this->end(); ?>