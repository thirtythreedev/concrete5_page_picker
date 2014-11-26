$(function(){

    $(".page_sort").sortable();

    $(".page_sort").on('click', '.js-delete-button', function(e){
        e.preventDefault();
        $('#page--' + $(this).data('cid')).remove();
        updateCount();
    });

    $('#page-table').on('click', '.js-site-add', function(e){
        e.preventDefault();

        var $this = $(this),
            href  = $this.data('url'),
            cid   = $this.data('cid'),
            title = $this.data('title');

        var str  = '';
            str += '<li id="page--'+ cid +'" class="relationship-list__item">';
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

    var pagePickerTabSetup = function() {
        $('ul#ccm-pagepicker-tabs li a').each( function(num,el){
            el.onclick=function(){
                var pane=$(this).attr('href');
                //console.log(pane);

                pagepickerShowPane(pane);
            }
        });
    };

    var pagepickerShowPane = function (pane){
        $('ul#ccm-pagepicker-tabs li a').each(function(num,el){ $(el).parent().removeClass('active') });
        $('a[href$="'+pane+'"]').parent().addClass('active');
        $('.tab-pane').each(function(num,el){ el.style.display='none'; });
        //console.log(pane);
        $(pane).css('display','block');
    };

    pagePickerTabSetup();

    $('#page-table').DataTable({
        initComplete: function () {
            var api = this.api();
            var column = api.column( 1 );
            var select = $('<select><option value="">All Page Types</option></select>')
                .appendTo( $(column.header()).empty() )
                .on( 'change', function () {
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );

                    column
                        .search( val ? '^'+val+'$' : '', true, false )
                        .draw();
                });

            column.data().unique().sort().each( function ( d, j ) {
                select.append( '<option value="'+d+'">'+d+'</option>' )
            });
        },
        dom: 'ftip',
        "columns": [
            null,
            { "orderable": false },
            ]
        }
    );

});
