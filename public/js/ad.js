let idCount = $('#ad_images div.form-group').length;
    $('#add-image').click(function(){
        // je récupère le numéro des futurs champs que je vais créer
        const index = idCount;
        idCount++;
        console.log(index);
        // je récupère le prototype des entrées
        const tmpl = $('#ad_images').data('prototype').replace(/__name__/g,index);

        // j'injecte le prototype form au sein de la div
        $('#ad_images').append(tmpl);

        

        // Je gère le bouton supprimer
        handleDeleteButtons();
    });

    function handleDeleteButtons(){
        $('button[data-action="delete"]').click(function(){
            const target = this.dataset.target;
            $(target).remove();
        });
    }
    handleDeleteButtons();