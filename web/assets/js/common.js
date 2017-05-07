$(function() {
    //default material events activate
    $(".button-collapse-custom").sideNav();
    $(".dropdown-button").dropdown({ hover: true });
    $('.modal').modal();

    // custom sub dropdown menu
    $('.subdropdown-button').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrain_width: true,
            hover: true,
            gutter: 0,
            belowOrigin: true,
            alignment: 'left'
        }
    );
});