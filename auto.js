$(function(){

    $('#page-table').DataTable();

    $(".page_sort").sortable();

    $(".page_sort").on('click', '.js-delete-button', function(e){
        e.preventDefault();
        $('#page--' + $(this).data('cid')).remove();
        updateCount();
    });

    $('#page-table').on('click', '.js-site-add', function(e){
        e.preventDefault();

        var $this = $(this),
            href = $this.attr('href'),
            cid = $this.data('cid'),
            title = $this.text();

        var str = '';
            str += '<li id="page--'+ cid +'">';
            str += '<a href="' + href + '" data-cid="'+ cid +'">' + title;
            str += '<span class="acf-button-remove"></span>';
            str += '</a>';
            str += '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
            str += '<input type="hidden" name="cids[]" value="'+ cid +'">';
            str += '<a class="js-delete-button" alt="Delete Page" data-cid="'+ cid +'"><span class="ui-icon ui-icon-circle-close"></span></a>';
            str += '</li>';

        $('.page_sort').append(str);

        updateCount();

    });

    // updates the right col count
    function updateCount(){
        $('#page_count').find('.number').text( $('.page_sort li').length );
    }

    updateCount();

});