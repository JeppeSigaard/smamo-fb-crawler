// Makes async call to smamo_crawl
jQuery(function(){
    var btn = jQuery('.settings_page_crawl .button-primary'),
        loading = false,

    createLog = function(c,overwrite){
        var log = jQuery('#crawl_settings_log');

        if(!log.length){
            console.log('creating logger');
            log = jQuery('<textarea id="crawl_settings_log" disabled rows="20" style="width: 100%; resize: none; font-size: 10px;"></textarea>');
            log.appendTo('form');
        }


        if(typeof overwrite !== 'undefined' && overwrite){
            log.val(c);
        }

        else{
            var allContent = log.val() + c;
            log.val(allContent);
        }

    };

    btn.on('click', function(e){
        e.preventDefault();

        if(!loading){
            btn.removeClass('button-primary').css({
                    'width' : '170px',
                    'text-align' :'left'
                }).val('Crawler Facebook...');
            loading = true;

            var i = '',
            simpleLoad = setInterval(function(){
                i += '.';
                if(i.length > 3 ){ i = '';}

                btn.val('Crawler Facebook'+i);

            },500);


            var cDo = jQuery('#crawl_settings_sammo_crawl_do_0').val(),
            cId = jQuery('#crawl_settings_sammo_crawl_id_0').val(),
            cUp = jQuery('#crawl_settings_sammo_crawl_update_old_0').val();

            if(cDo == 'crawl_events'){

                createLog('Odaterer alle steder og begivenheder<br><br>', true);

                jQuery.post({
                    url: ajaxurl,
                    dataType : 'json',
                    data: {
                        action : 'smamo_crawl',
                        do : 'crawl',
                        update_old : cUp,
                    },

                    success : function(ret){
                        if(ret.locations){

                            createLog(JSON.stringify(ret), false);

                            jQuery.post({
                                url: ajaxurl,
                                dataType : 'json',
                                data: {
                                    action : 'smamo_crawl',
                                    do : 'events',
                                    update_old : cUp,
                                },
                                success : function(answ){
                                    btn.addClass('button-primary').removeAttr('style').val('Færdig! klik for at crawle igen');
                                    clearInterval(simpleLoad);
                                    loading = false;

                                    createLog(JSON.stringify(answ), false);
                                }

                            });
                        }
                    }
                });
            }

            else{

                var data = {
                    action : 'smamo_crawl',
                    do : cDo,
                    update_old : cUp,
                }

                if(cId !== ''){
                    data.id = cId;
                }

                createLog('Forespørgsel sendt, venter på svar', true);

                jQuery.post({
                    url: ajaxurl,
                    dataType : 'json',
                    data: data,
                    success : function(ret){
                        btn.addClass('button-primary').removeAttr('style').val('Færdig! klik for at crawle igen');
                        clearInterval(simpleLoad);
                        loading = false;

                        createLog(JSON.stringify(ret), true);

                    }
                });
            }
        }
    });
});
