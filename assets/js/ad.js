$('#add-image').click(function (){

    //recuperer le numero des futurs champs que je vais créer
    //const index = $('#ad_images div.form-group').length;
    const index = +$('#widget-counters').val();

    //Je recupere le prototype des entrées
    const tmpl=$('#ad_images').data('prototype').replace(/__name__/g,index);
    //console.log(tmpl);
    //J'injecte ce code dans la div
    $('#ad_images').append(tmpl);

    $('#widget-counters').val(index+1);

    handleDeleteButtons();
});
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        //console.log(target);
        $("div#"+target).remove();
    });
}
function updateCounter() {
    const index = +$('#ad_images div.form-group').length;
    $('#widget-counters').val(index);
}
updateCounter();
handleDeleteButtons();