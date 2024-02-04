
var SmartChurch = {
    __data: [],
    generateSelect: function(id, options, var_value, var_label, var_label_2) {
        var opts = '<option></option>';
        for(var k in options) {
            var value = options[k][var_value];
            var label = options[k][var_label];
            if(var_label_2 != undefined) {
                label += ' - ' + options[k][var_label_2];
            }
            opts += '<option value="'+value+'">'+label+'</option>';
        }
        jQuery('#'+id).html(opts);
    },
    onSelectSinodo: function() {
        var presbiterios = SmartChurch.__data.presbiterios;
        if(jQuery(this).val() != '') {
            presbiterios = SmartChurch.__data.presbiterios.filter(x => x.sinodo == jQuery(this).val());
        }
        SmartChurch.generateSelect('presbiterio', presbiterios, 'id', 'sigla', 'nome');
        var igrejas = SmartChurch.__data.igrejas;
        if(jQuery(this).val() != '') {
            igrejas = SmartChurch.__data.igrejas.filter(x => x.sinodo == jQuery(this).val());
        }
        SmartChurch.generateSelect('igreja', igrejas, 'id', 'nome');
    },
    onSelectPresbiterio: function() {
        var igrejas = SmartChurch.__data.igrejas;
        if(jQuery(this).val() != '') {
            igrejas = SmartChurch.__data.igrejas.filter(x => x.presbiterio == jQuery(this).val());
        }
        SmartChurch.generateSelect('igreja', igrejas, 'id', 'nome');
    },
    onSelectSinodal: function() {
        var federacoes = SmartChurch.__data.federacoes;
        if(jQuery(this).val() != '') {
            federacoes = SmartChurch.__data.federacoes.filter(x => x.sinodal == jQuery(this).val());
        }
        SmartChurch.generateSelect('federacao', federacoes, 'id', 'sigla', 'nome');
        var sociedades = SmartChurch.__data.sociedades;
        if(jQuery(this).val() != '') {
            sociedades = SmartChurch.__data.sociedades.filter(x => x.sinodal == jQuery(this).val());
        }
        SmartChurch.generateSelect('sociedade', sociedades, 'id', 'nome');
    },
    onSelectFederacao: function() {
        var sociedades = SmartChurch.__data.sociedades;
        if(jQuery(this).val() != '') {
            sociedades = SmartChurch.__data.sociedades.filter(x => x.federacao == jQuery(this).val());
        }
        SmartChurch.generateSelect('sociedade', sociedades, 'id', 'nome');
    },
    onSelectCargo: function() {
        var cargo = SmartChurch.__data.cargos.find(x => x.id == jQuery(this).val());
        if(cargo != undefined) {
            jQuery('#cargo_ref').val(cargo.instancia);
        }
    },
    init: function() {
        if(jQuery('#smartchurchData').length > 0) {
            this.__data = jQuery.parseJSON(atob(jQuery('#smartchurchData').html()));
            jQuery('#sinodo').change(this.onSelectSinodo);
            jQuery('#presbiterio').change(this.onSelectPresbiterio);
            jQuery('#sinodal').change(this.onSelectSinodal);
            jQuery('#federacao').change(this.onSelectFederacao);
            jQuery('#cargo').change(this.onSelectCargo);
        }
    }
};


