<div class="container">
    <div class="row">
        <div class="xs-12 md-6 mx-auto">
            <div id="countUp">
                <div class="number">Ouuups ....</div>
                <div class="text">Something has gone wrong. This may not mean anything.</div>
                <div class="text">I'm probably working on something that has blown up.</div>
                <a href="<?php echo \Application::getRoute('index', 'index')?>"> > Home</a>
            </div>
        </div>
    </div>
</div>       

<style>

#contentPage {
     background: #948e99; 
    background: -webkit-linear-gradient(to right, #948e99, #2e1437); 
    background: linear-gradient(to right, #948e99, #2e1437);
    background-size: cover;
    background-repeat: no-repeat;
    font-family: "Roboto Mono", "Liberation Mono", Consolas, monospace;
    color: rgba(255,255,255,.87);
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.container,
.container > .row,
.container > .row > div {
    height: 100%;
}

#countUp {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.number {
    font-size: 35px;
    font-weight: 500;
}
.text {
    margin: 0 0 1rem;
}

.text {
    font-weight: 300;
    text-align: center;
}

a{color: #fff}
a:hover{color: #fff}

</style>
            