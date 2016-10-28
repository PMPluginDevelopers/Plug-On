<div class="loading-wall">
    <img src="/images/loading.gif" width="48" height="48"/>
</div>
<script type="text/javascript">
    $(".loading-wall").hide();
</script>
<style type="text/css">
    .loading-wall {
        position: fixed;
        height: 100%;
        width: 100%;
        padding: 100%;
        margin: 0;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 50;
    }
    .loading-wall img {
        z-index: 60;
        margin: auto auto;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>