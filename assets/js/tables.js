jQuery(document).ready(function($) {
    const sortTable = (table, target, order) => {

        // find the nearest header, find its index(starts with 0)
        let columnIndex = $(target).closest('th').index();

        //find all rows inside tbody
        let rows = $(table).find('tbody tr').get(); 

        rows.sort(function(a, b) {
            let cellA = $(a).children('td').eq(columnIndex).text().toLowerCase();
            let cellB = $(b).children('td').eq(columnIndex).text().toLowerCase();
    
            if (order === 'asc') {
                return cellA.localeCompare(cellB);
            } else {
                return cellB.localeCompare(cellA);
            }
        });
    
        $.each(rows, function(index, row) {
            $(table).children('tbody').append(row);
        });
    };

    const filterTable = (table, target, byValue) => {
        let columnIndex = $(target).closest('th').index();

        let rows = $(table).find('tbody tr').get();
        
        rows = rows.filter(function(row){
            let value = $(row).children('td').eq(columnIndex).text().toLowerCase();
            return value == byValue;
        });
        
        $(table).children('tbody').empty();
        $.each(rows, function(index, row) {
            $(table).children('tbody').append(row);
        });
    };

    const updateTable = (table, whichPage) => {
        let config = table.data('config') || {};
        $.ajax({
            url: my.ajaxUrl,
            type: 'post',
            data: {
                action: 'cdt_page_data',
                data: {
                    page: whichPage,
                    config,
                    nonce: my.securityNonce,
                },
            }
        })
        .done(function(response){
            if ( response.success ) {
                table.children('tbody').empty();
                table.children('tbody').append(response.data);
            }else{
                console.log(response);
            }
        })
        .fail(function(){
            alert('failed to load information')
        })
        ;
    };

    $('table>thead>tr>th .asc').click(function(e) {
        let table = $("#" + my.tableId);
        
        let config = table.data('config') || {};
        let name = $(e.currentTarget).closest('th').find('.column').text();
        config[name] = 'asc';        
        table.data('config', config);

        sortTable(table, e.currentTarget, 'asc');
    });
    
    $('table>thead>tr>th .desc').click(function(e) {
        let table = $("#" + my.tableId);

        let config = {};
        let name = $(e.currentTarget).closest('th').find('.column').text();
        config[name] = {};
        config[name]['ordering'] = 'desc';
        let oldConfig = table.data('config') || {};
        config = Object.assign(config, oldConfig);
        table.data('config', config);
        
        sortTable(table, e.currentTarget, 'desc');
    });

    $('table>thead>tr>th .filter').click(function(e) {
        //open a modal for filtering
        $('table>thead>tr>th .filterModal').show();
    });
    
    $('table>thead>tr>th .filterModalSelect').change(function(e) {
        let byValue = e.currentTarget.value;
        let table = $("#" + my.tableId);
        let name = $(e.currentTarget).closest('th').find('.column').text();

        let config = {};
        config[name] = {};
        config[name]['filtering'] = byValue;
        let oldConfig = table.data('config') || {};
        config = Object.assign(config, oldConfig);
        table.data('config', config);

        filterTable(table, e.currentTarget, byValue);
    });

    $('.pagination a.prev').click(function(e) {
        let table = $("#" + my.tableId);
        let currentPage = table.data('current-page') || 1;
        let whichPage = currentPage - 1;
        updateTable(table, whichPage);
    });

    $('.pagination a.next').click(function(e) {
        let table = $("#" + my.tableId);
        let currentPage = table.data('current-page') || 1;
        let whichPage = currentPage + 1;
        updateTable(table, whichPage);
    });

    $('.pagination a.page').click(function(e){
        let table = $("#" + my.tableId);
        let whichPage = $(e.currentTarget).text();
        updateTable(table, whichPage);
    });
});

