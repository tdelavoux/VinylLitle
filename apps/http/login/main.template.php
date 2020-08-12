<div id="login-Page-101">
    <div id="centered-bloc" class="col-md-4">
        <h3 class="text-center">Login</h3>

        <form  action="<?php echo application ::getRoute('login', 'login') ?> " method="post">
            
            <div class="row">
                <div class="form101-line">
                    <img src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>images/vinyl.svg" height="40px" />
                    <p>Hey I'm Viny, seems we haven't met yet, what is your name ?</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="login">Name</label>
                <input id="login" placeholder="Dupont" name="login" type="text" class="form-control">
            </div>

            <div class="form-btn text-center">
                <button type="submit" class="btn btn-primary">Login <i class="fas fa-arrow-right"></i></button>
            </div>
        </form>	
    </div>
</div>