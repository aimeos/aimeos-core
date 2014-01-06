/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: Filter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

/**
 * operator and value part of a search criteria 
 * 
 * @namespace   Ext.ux.AdvancedSearch
 * @class       Ext.ux.AdvancedSearch.Filter
 * @extends     Ext.Container
 */
Ext.ux.AdvancedSearch.Filter = Ext.extend(Ext.Container, {
    
    defaultOperator: null,
    
    defaultValue: null,
    
    operator: null,
    operatorFieldConfig: null,
    
    value: null,
    valueFieldConfig: null,
    
    layout: 'hbox',
    layoutConfig: {
        pack  : 'start'
    },
    
    getOperator: function() {
        return this.operatorField.getValue();
    },
    
    getValue: function() {
        return this.valueField.getValue();
    },
    
    initComponent: function() {
        this.initOperatorField();
        this.initValueField();

        this.items = [
            Ext.applyIf(this.operatorField, {
                flex: 1
            }),
            Ext.applyIf(this.valueField, {
                flex: 2
            })
        ];
        
        Ext.ux.AdvancedSearch.Filter.superclass.initComponent.call(this);
    },
    
    initOperatorField: function() {
        this.operatorStore = new Ext.data.ArrayStore({
            fields: ['operator', 'displayText']
        });
        
        Ext.each(this.operators, function(operator) {
            this.operatorStore.loadData([[operator, _(operator)]], true);
        }, this);
        
        this.operatorField = Ext.ComponentMgr.create(Ext.apply({
            xtype: 'combo',
            typeAhead: true,
            triggerAction: 'all',
            lazyRender:true,
            emptyText: _('select an operator'),
            forceSelection: true,
            mode: 'local',
            store: this.operatorStore,
            valueField: 'operator',
            displayField: 'displayText',
            isValid: function(preventMark) {
                var isValid = Ext.form.ComboBox.prototype.isValid.apply(this, arguments),
                    val = this.getRawValue(),
                    rec = this.findRecord(this.valueField, val);
                    
                if (!isValid || ! rec) {
                    if (! preventMark) {
                        this.markInvalid(this.blankText);
                    }
                    return false;
                }
                
                return true;
            },
            value: this.operator ? this.operator : this.defaultOperator,
            listeners: {
                scope: this,
                select: this.onOperatorSelect
            }
        }, this.operatorFieldConfig));
    },
    
    initValueField: function() {
        this.valueField = Ext.ComponentMgr.create(Ext.apply({
            xtype: 'textfield',
            selectOnFocus: true,
            listeners: {
                scope: this,
                specialkey: function(field, e) {
                    if (e.getKey() == e.ENTER) {
                        this.fireEvent('filtertrigger', this);
                    }
                }
            },
            isValid: function(preventMark) {
                var isValid = Ext.form.TextField.prototype.isValid.apply(this, arguments),
                    val = this.getRawValue();
                    
                if (!isValid || ! Ext.isString(val)) {
                    if (! preventMark) {
                        this.markInvalid();
                    }
                    return false;
                }
                
                return true;
            }
        }, this.valueFieldConfig));
    },
    
    isValid: function(preventMark) {
        return this.isValidOperator(preventMark) && this.isValidValue(preventMark);
    },
    
    isValidOperator: function(preventMark) {
        return this.operatorField.isValid(preventMark);
    },
    
    isValidValue: function(preventMark) {
        return this.valueField.isValid(preventMark);
    },
    
    onOperatorSelect: function(combo, newRecord, newKey) {
        
    },
    
    setOperator: function(operator) {
        this.operatorField.setValue(operator);
        return this;
    },
    
    setValue: function(value) {
        this.valueField.setValue(value);
        return this;
    }
});/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: FilterCriteria.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

/**
 * dataIndex chooser + filter 
 * 
 * @namespace   Ext.ux.AdvancedSearch
 * @class       Ext.ux.AdvancedSearch.FilterCriteria
 * @extends     Ext.Container
 */
Ext.ux.AdvancedSearch.FilterCriteria = Ext.extend(Ext.Container, {
    
    layout: 'hbox',
    layoutConfig: {
        pack  : 'start'
    },
    filterLayoutConfig: {
        flex: 3
    },
    
    createFilter: function(r) {
        var def = r.get('definition'),
            filter = Ext.ComponentMgr.create(def),
            defValue  = filter.getValue(),
            defOp     = filter.getOperator();
        
        filter.setOperator(this.initialConfig.filter.operator);
        if (! filter.isValidOperator(true)) {
            filter.setOperator(defOp);
        }
        
        filter.setValue(this.initialConfig.filter.value);
        if (! filter.isValidValue(true)) {
            filter.setValue(defValue);
        }
        
        this.relayEvents(filter, ['filtertrigger']);
        
        Ext.apply(filter, this.filterLayoutConfig);
        return filter;
    },
    
    getFilterData: function() {
        return {
            dataIndex : this.CriteriaCombo.getValue(),
            operator  : this.filter.getOperator(),
            value     : this.filter.getValue() || ''
        };
    },
    
    initComponent: function() {
        this.CriteriaCombo = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            lazyRender:true,
            forceSelection: true,
            emptyText: _('select a criteria'),
            mode: 'local',
            store: this.filterModel,
            valueField: 'dataIndex',
            displayField: 'label',
            value: this.filter.dataIndex,
            listeners: {
                scope: this,
                select: this.onCriteriaSelect
            }
        });
        
        this.filter = this.createFilter(this.filterModel.getById(this.filter.dataIndex));
        
        this.items = [
            Ext.applyIf(this.CriteriaCombo, {
                flex: 2
            }),
            Ext.applyIf(this.filter, {
                flex: 3
            })
        ];
        
        Ext.ux.AdvancedSearch.FilterCriteria.superclass.initComponent.call(this);
    },
    
    onCriteriaSelect: function(combo, newRecord, newKey) {
        var newFilter = this.createFilter(newRecord),
            defValue  = newFilter.getValue(),
            defOp     = newFilter.getOperator(),
            oldValue  = this.filter.getValue(),
            oldOp     = this.filter.getOperator();
        
        // probe the old operator / value
        newFilter.setOperator(oldOp);
        if (! newFilter.isValidOperator(true)) {
            newFilter.setOperator(defOp);
        }
        
        newFilter.setValue(oldValue);
        if (! newFilter.isValidValue(true)) {
            newFilter.setValue(defValue);
        }
        
        // replace filter
        this.add(newFilter);
        this.remove(this.filter, true);
        this.doLayout();
        
        this.filter = newFilter;
    },
    
    setFilterData: function(value) {
        if (value.hasOwnProperty('dataIndex')) {
            this.CriteriaCombo.setValue(value.dataIndex);
        }
        if (value.hasOwnProperty('operator')) {
            this.filter.setOperator(value.operator);
        }
        if (value.hasOwnProperty('value')) {
            this.filter.setValue(value.value);
        }
        
        return this;
    }
});

Ext.reg('ux.filtercriteria', Ext.ux.AdvancedSearch.FilterCriteria);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: FilterGroup.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.FilterGroup = Ext.extend(Ext.Container, {
    /**
     * @cfg {Array} filterModel (required)
     */
    filterModel: null,
    
    /**
     * @cfg {String} defaultFilter dataIndex of default filter
     */
    defaultFilter: null,
    
    combineOperator: 'AND',
    
    layout: 'vbox',
    border: false,
    
    layoutConfig: {
        align : 'stretch',
        pack  : 'start'
    },
    
    defaults: {
        border: false
    },
    
    getHeight: function() {
        var height = 0;
        
        this.items.each(function(item) {
            height += item.getHeight();
        }, this);
        return height;
    },
    
    getFilterData: function() {
        var filters = [];
        this.items.each(function(item) {
            if (item.criteria) {
                filters.push(item.criteria.filter.getFilterData());
            }
        }, this);
        
        return {
            condition: this.combineOperator,
            filters: filters
        };
    },
    
    addFilter: function(filter) {
        var criteria = {};
        
        criteria.filter = Ext.ComponentMgr.create({
            xtype: 'ux.filtercriteria',
            flex: 1,
            filterModel: this.filterModel,
            filter: filter
        });
        criteria.addBtn = new Ext.Button({
//            text: _('add'),
            iconCls: 'ux-advancedsearch-action-addcriteria',
            handler: this.onAddBtnClick.createDelegate(this, [criteria])
        });
        criteria.delBtn = new Ext.Button({
//            text: _('del'),
            iconCls: 'ux-advancedsearch-action-delcriteria',
            handler: this.onDelBtnClick.createDelegate(this, [criteria])
        });
        
        criteria.cmp = Ext.ComponentMgr.create({
            xtype: 'container',
            layout: 'hbox',
            layoutConfig: {align: 'stretchmax'},
            items: [criteria.addBtn, criteria.delBtn, criteria.filter],
            criteria: criteria
        });
        
        this.add(criteria.cmp);
        
        // relayout
        this.doLayout();
        if (this.ownerCt) {
            this.ownerCt.doLayout();
        }
        
        this.relayEvents(criteria.filter, ['filtertrigger']);
    },
    
    afterRender: function() {
        Ext.ux.AdvancedSearch.FilterGroup.superclass.afterRender.apply(this, arguments);
        this.getEl().on('contextmenu', this.onContextMenu, this);
    },
    
    doLayout: function() {
        // arrange buttons
        var lastIdx = this.items.getCount() -1;
        this.items.each(function(item, idx) {
            item.criteria.addBtn.setDisabled(idx !== lastIdx);
            item.criteria.delBtn.setDisabled(lastIdx === 0);
        }, this);
        
        Ext.ux.AdvancedSearch.FilterGroup.superclass.doLayout.apply(this, arguments);
    },
    
    initComponent: function() {
        Ext.ux.AdvancedSearch.FilterGroup.superclass.initComponent.call(this);
        
        // init fieldStore
        var data = [];
        Ext.each(this.filterModel, function(filter) {
            data.push([filter.dataIndex, filter.label, filter]);
        }, this);
        this.filterModel = new Ext.data.ArrayStore({
            idIndex: 0,
            fields: ['dataIndex', 'label', 'definition'],
            data: data
        });
        
        // init default filter
        if (! this.defaultFilter) {
            this.defaultFilter = this.filterModel.getAt(0).get('dataIndex');
        }
        if (Ext.isString(this.defaultFilter)) {
            this.defaultFilter = {dataIndex: this.defaultFilter};
        }
        
        // init filters
        if (! Ext.isArray(this.filters) || Ext.isEmpty(this.filters)) {
            this.filters = [this.defaultFilter];
        }
                
        // add filters to this group
        Ext.each(this.filters, function(filter) {
            this.addFilter(filter);
        }, this);
        
        this.action_reset = new Ext.Action({
            text: _('Reset all filters in this group'),
            iconCls: 'ux-advancedsearch-action-resetgroup',
            handler: this.resetFilters,
            scope: this
        });
    },
    
    onAddBtnClick: function(criteria) {
        this.addFilter(this.defaultFilter);
    },
    
    onContextMenu: function(e) {
        e.preventDefault();
        if (! this.menu) {
            this.menu = new Ext.menu.Menu({
                items: this.action_reset
            });
        }
        
        this.menu.showAt(e.getXY());
    },
    
    onDelBtnClick: function(criteria) {
        this.remove(criteria.cmp, true);
        
        this.doLayout();
        if (this.ownerCt) {
            this.ownerCt.doLayout();
        }
        
        this.fireEvent('filtertrigger', this);
    },
    
    resetFilters: function() {
        this.items.each(function(item, idx) {
            this.remove(item.criteria.cmp, true);
        }, this);
        
        Ext.each(this.filters, function(filter) {
            this.addFilter(filter);
        }, this);
        
        this.doLayout();
        if (this.ownerCt) {
            this.ownerCt.doLayout();
        }
        
        this.fireEvent('filtertrigger', this);
    },

    
    setFilterData: function( critera, operator ) {
        
        this.items.each( function( item, idx ) {
            this.remove( item.criteria.cmp, true );
        }, this );
        
        critera = Ext.isArray( critera ) ? critera : [critera];
        Ext.each( critera, function( criterium ) {
            this.addFilter( criterium );
        }, this );
        
        this.combineOperator = operator || 'AND';
        
        this.doLayout();
        if( this.ownerCt ) {
            this.ownerCt.doLayout();
        }
        
        this.fireEvent( 'filtertrigger', this );
    }
    
});

Ext.reg('ux.filtergroup', Ext.ux.AdvancedSearch.FilterGroup);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TextFilter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.TextFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {
    
    defaultOperator: 'startswith',
    
    defaultValue: '',
    
    // _('equals') _('contains') _('startswith') _('endswith')
    operators: ['equals', 'contains', 'startswith', 'endswith'],
    
    initComponent: function() {
        
        Ext.ux.AdvancedSearch.TextFilter.superclass.initComponent.call(this);
    }
    
    
});

Ext.reg('ux.textfilter', Ext.ux.AdvancedSearch.TextFilter);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: NumberFilter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.NumberFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {
    
    defaultOperator: 'equals',
    
    defaultValue: '',
    
    // _('equals') _('greaterequals') _('lessequals') _('greater') _('less')
    operators: ['equals', 'greaterequals', 'lessequals', 'greater', 'less'],
    
    initComponent: function() {
        
        Ext.ux.AdvancedSearch.NumberFilter.superclass.initComponent.call(this);
    }
    
    
});

Ext.reg('ux.numberfilter', Ext.ux.AdvancedSearch.NumberFilter);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: BooleanFilter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.BooleanFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {
    
    defaultOperator: 'equals',
    
    defaultValue: '',
    
    // _('equals')
    operators: ['equals'],
    
    initComponent: function() {
        
        Ext.ux.AdvancedSearch.BooleanFilter.superclass.initComponent.call(this);
    }
    
    
});

Ext.reg('ux.booleanfilter', Ext.ux.AdvancedSearch.BooleanFilter);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: DateFilter.js 14664 2012-01-03 16:38:41Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.DateFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {

	defaultOperator : 'equals',

	defaultValue : '',

	// _('equals') _('before') _('after')
	operators : ['equals', 'before', 'after'],


	initComponent : function() {

		this.valueFieldConfig = {
			xtype: 'datefield',
			format: 'Y-m-d H:i:s'
		};

		Ext.ux.AdvancedSearch.DateFilter.superclass.initComponent.call(this);
	},


	getValue : function() {
		
		v = Ext.ux.AdvancedSearch.DateFilter.superclass.getValue.call(this);

		if( v ) {
			return new Date( v ).format( 'Y-m-d H:i:s' );
		}

		return v;
	}
});

Ext.reg( 'ux.datefilter', Ext.ux.AdvancedSearch.DateFilter );
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: AdvancedSearch.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux');

Ext.ux.AdvancedSearchPanel = Ext.extend(Ext.Panel, {

    
    /**
     * @cfg {String} searchParam
     * search parameter name (defaults to 'condition') 
     */
    searchParam: 'condition',
    
    /**
     * @cfg {Ext.data.Store} store
     * The {@link Ext.data.Store} the advancedSearch should use as its data source (required).
     */
    
    boxMaxWidth: 700,
    
    cls: 'ux-advancedsearch ux-advancedsearch-panel x-toolbar',
    layout: 'hbox',
    layoutConfig: {
        align : 'stretch',
        pack  : 'start'
    },
    
    
    border: false,
    
    initComponent : function(){
        this.bindStore(this.store, true);
        
        //this.searchBox = new Ext.form.TextField({});
        this.searchBtn = new Ext.Button({
            flex: 0,
            text: _('Search'),
            scope: this,
            handler: this.doLoad
        });
        
        this.resetBtn = new Ext.Button({
            flex: 0,
            //text: _('Reset'),
            iconCls: 'ux-advancedsearch-action-resetall',
            scope: this,
            handler: this.doReset
        });
        
        this.filterGroup = Ext.ComponentMgr.create({
            xtype: 'ux.filtergroup',
            flex: 1,
            filterModel: this.filterModel,
            filters: this.filters,
            listeners: {filtertrigger: this.doLoad.createDelegate(this)}
        });
        
        this.items = [
            this.filterGroup, {
                border: false,
                layout: 'vbox',
                flex: 0,
                layoutConfig: {
                    pack: 'end'
                },
                items: {
                    flex: 0,
                    border: false,
                    width: 100,
                    layout: 'hbox',
                    layoutConfig: {
                        align : 'stretchmax',
                        pack  : 'start'
                    },
                    items: [
                        this.searchBtn,
                        this.resetBtn
                    ]
                }
            }
        ];
        
        Ext.ux.AdvancedSearchPanel.superclass.initComponent.call(this);
    },
    
    // @todo generalize
    beforeLoad : function(store, options){
        options.params = options.params || {};
        options.params[this.searchParam] = options.params[this.searchParam] || {};
        options.params[this.searchParam]['&&'] = options.params[this.searchParam]['&&'] || [];
        
        
        var filterData = this.filterGroup.getFilterData(),
            pn = this.getPolishNotation(filterData);
        
        options.params[this.searchParam]['&&'] = options.params[this.searchParam]['&&'].concat(pn['&&']);
        
        if(this.rendered && this.refresh){
            this.searchBtn.disable();
        }
    },
    
    doLayout: function() {
        if (this.filterGroup && this.filterGroup.rendered) {
            this.setHeight(this.filterGroup.getHeight());
        }
        
        Ext.ux.AdvancedSearchPanel.superclass.doLayout.apply(this, arguments);
    },
    
    // private
    doLoad : function(){
        if(this.fireEvent('beforechange', this) !== false){
            this.store.load();
        }
    },
    
    doReset: function() {
        this.filterGroup.resetFilters();
    },
    
    // TODO move this to some sort of `serializer`
    getPolishNotation: function(filterData) {
        var pnGroup = {},
            pnFilters = [],
            pnOpMap = {
                'equals' : '==',
                'contains': '~=',
                'startswith': '=~',
                'greaterequals': '>=',
                'lessequals': '<=',
                'greater': '>',
                'less': '<',
                'after': '>',
                'before': '<'
            };

        Ext.each(filterData.filters, function(filter) {
            if (filter.hasOwnProperty('condition')) {
                pnFilters.push(this.getPolishNotation(filter));
            } else {
                var pnCrit = {},
                    pnVal = {};
                    
                pnVal[filter.dataIndex] = filter.value;
                pnCrit[pnOpMap[filter.operator]] = pnVal;
                
                pnFilters.push(pnCrit);
            }
        }, this);
        
        pnGroup[filterData.condition == 'AND' ? '&&' : '||'] = pnFilters;
        
        return pnGroup;
    },
    
    // private
    onLoad : function(store, r, o){
        if(!this.rendered){
            this.dsLoaded = [store, r, o];
            return;
        }
        
        this.searchBtn.enable();
    },
    
    // private
    onLoadError : function(){
        if(!this.rendered){
            return;
        }
        this.searchBtn.enable();
    },
    
    /**
     * Binds the paging toolbar to the specified {@link Ext.data.Store}
     * @param {Store} store The store to bind to this toolbar
     * @param {Boolean} initial (Optional) true to not remove listeners
     */
    bindStore : function(store, initial){
        var doLoad;
        if(!initial && this.store){
            if(store !== this.store && this.store.autoDestroy){
                this.store.destroy();
            }else{
                this.store.un('beforeload', this.beforeLoad, this);
                this.store.un('load', this.onLoad, this);
                this.store.un('exception', this.onLoadError, this);
            }
            if(!store){
                this.store = null;
            }
        }
        if(store){
            store = Ext.StoreMgr.lookup(store);
            store.on({
                scope: this,
                beforeload: this.beforeLoad,
                load: this.onLoad,
                exception: this.onLoadError
            });
            doLoad = true;
        }
        this.store = store;
        if(doLoad){
            this.onLoad(store, null, {});
        }
    },

    // private
    onDestroy : function(){
        this.bindStore(null);
        Ext.ux.AdvancedSearchPanel.superclass.onDestroy.call(this);
    }
});

Ext.reg('ux.advancedsearch', Ext.ux.AdvancedSearchPanel);/*
 * Tine 2.0
 * 
 * @license     MIT, BSD, and GPL
 * @author      Cornelius Weiss <c.weiss@metaways.de>
 * @copyright   Copyright (c) 2009-2010 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id: JsonRpcProvider.js 11208 2011-04-16 14:00:52Z nsendetzky $
 */

Ext.ns('Ext.ux.direct');

/**
 * @namespace   Ext.ux.direct
 * @class       Ext.ux.direct.JsonRpcProvider
 * @extends     Ext.direct.RemotingProvider
 * @author      Cornelius Weiss <c.weiss@metaways.de>
 * @copyright   Copyright (c) 2009-2010 Metaways Infosystems GmbH (http://www.metaways.de)
 * 
 * Ext.Direct provider for seamless integration with JSON RPC servers
 * 
 *  Ext.Direct.addProvider(Ext.apply(Ext.app.JSONRPC_API, {
        'type'     : 'jsonrpcprovider',
        'url'      : Ext.app.JSONRPC_API
    }));
 * 
 */
Ext.ux.direct.JsonRpcProvider = Ext.extend(Ext.direct.RemotingProvider, {
    
    /**
     * @cfg {Boolean} paramsAsHash
     */
    //paramsAsHash: true,
    
    /**
     * @cfg {Boolean} useNamedParams
     */
    useNamedParams: false,
    
    // private
    initAPI : function() {
        for (var method in this.services){
            var mparts = method.split('.');
            var cls = this.namespace[mparts[0]] || (this.namespace[mparts[0]] = {});
            cls[mparts[1]] = this.createMethod(mparts[0], Ext.apply(this.services[method], {
                name: mparts[1],
                len: this.services[method].parameters.length
            }));
        }
    },
    
    // private
    doCall : function(c, m, args) {
        // support named/hashed parameters e.g. from DirectProxy
        if (args[args.length-1].paramsAsHash) {
            var o = args.shift();
            for (var i = 0; i < m.parameters.length; i++) {
                args.splice(i,0, o[m.parameters[i].name]);
            }
        }
        
        return Ext.ux.direct.JsonRpcProvider.superclass.doCall.call(this, c, m, args);
    },
    
    // private
    getCallData: function(t){
        var method = t.action + '.' + t.method;
        var m  = t.provider.services[method];
        var data = t.data || [];
        
        if (this.useNamedParams) {
            data = {};
            Ext.each(m.parameters, function(param, i) {
                data[param['name']] = t.data[i];
                
                /* NOTE: Ext.Direct and the automatically creaeted DirectFn's don't support optional params, cause 
                 * callback and scope are expected on fixed positions!
                 
                var value = typeof t.data[i] !== 'function' ? t.data[i] : undefined;
                
                if (value === undefined && ! param.optional) {
                    value = param['default'];
                }
                
                if (value !== undefined) {
                    data[param['name']] = value;
                }
                */
            }, this);
        }
        
        return {
            jsonrpc: '2.0',
            method: method,
            params: data,
            id: t.tid
        };
    },
    
    // private
    onData: function(opt, success, xhr) {
        var rs = [].concat(Ext.decode(xhr.responseText));
        
        xhr.responseText = [];
        Ext.each(rs, function(rpcresponse, i) {
            if(rpcresponse == undefined) {
            	rpcresponse = [];
            	rpcresponse.result = false;
            	rpcresponse.id = -1;
            	rpcresponse.error = 'The operation timed out';
            }
            
            xhr.responseText[i] = {
                type: rpcresponse.result ? 'rpc' : 'exception',
                result: rpcresponse.result,
                tid: rpcresponse.id,
                error: rpcresponse.error
            };
            
            if (xhr.responseText[i].type === 'rpc') {
                delete xhr.responseText.error;
            }
        });
        
        return Ext.ux.direct.JsonRpcProvider.superclass.onData.apply(this, arguments);
    }
});

Ext.Direct.PROVIDERS['jsonrpcprovider'] = Ext.ux.direct.JsonRpcProvider;/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: Function.deferByTickets.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.applyIf(Function.prototype, {
    
    /**
     * defers execution on a ticket bases. The function gets executed when all regisered tickets are back.
     * 
var sayHi = function(name){
    alert('Hi, ' + name);
}
    
var ticketFn = sayHi.deferByTickets(this, ['Fred']);

// take ticket
var ticketFn1 = ticketFn();
var ticketFn2 = ticketFn();

// give tickets back
 ticketFn1();
 ticketFn2();
 
     * NOTE: the function gets never executed if not at least one ticket is taken and given back
     * 
     * @param {Object} scope (optional) The scope (this reference) in which the function is executed.
     * @param {Array} args (optional) Overrides arguments for the call. (Defaults to the arguments passed by the caller)
     * @param {Boolean/Number} appendArgs (optional) if True args are appended to call args instead of overriding,
     * if a number the args are inserted at the specified position
     * @return {Function} ticketFn
     */
    deferByTickets: function(obj, args, appendArgs) {
        var fn = this.createDelegate(obj, args, appendArgs),
            waitTickets = [];
            
        // run if all tickets are back
        var run = function() {
            if (Ext.isEmpty(waitTickets)) {
                fn();
            }
        };
         
        return function() {
            var ticket = Ext.id();
            waitTickets.push(ticket);
            // fn to return wait ticket
            return function() {
                waitTickets.remove(ticket);
                run();
            };
        };
    }
});/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemRegistry.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux');

/**
 * plugin to insert additional registered items into a container
 *
 * @namespace   Ext.ux
 * @class       Ext.ux.ItemRegistry
 * @autor       Cornelius Weiss <c.weiss@metaways.de>
 * @license     BSD, MIT and GPL
 * @verstion    $Id: ItemRegistry.js 14263 2011-12-11 16:36:17Z nsendetzky $
 *
 * @example
// register 'additional item' for myDialog
Ext.ux.ItemRegistry.registerItem('myDialog', 'add-item-xtype', 20);

// in myDialog use itemRegistyPlugin
myDialog = Ext.extend(Ext.Container, {
    ...
    plugins: [{
        ptype: 'ux.itemregistry',
        key:   'myDialog'
    }]
})
 */
Ext.ux.ItemRegistry = function () {};

/**
 * @static
 * @private
 */
Ext.ux.ItemRegistry.itemMap = {};

/**
 * registers an item for a given key
 * @static
 * 
 * @param {String} key
 * @param {String/Constructor/Object} item
 * @param {Number} pos (optional)
 */
Ext.ux.ItemRegistry.registerItem = function(key, itemkey, item, pos) {
    if (! Ext.ux.ItemRegistry.itemMap.hasOwnProperty(key)) {
        Ext.ux.ItemRegistry.itemMap[key] = {};
    }

    Ext.ux.ItemRegistry.itemMap[key][itemkey] = {
        item: item,
        pos: pos
    };	
};

Ext.ux.ItemRegistry.prototype = {
    /**
     * @cfg {String} key
     * key the items are registered under. If no key is given, the itemId
     * of the component will be used
     */
    key: null,

    init: function(cmp) {
        this.cmp = cmp;

        if (! this.key) {
            this.key = cmp.getItemId();
        }
        
        // give static item pos to existing items
        this.cmp.items.each(function(item, idx) {
            if (! item.hasOwnProperty('registerdItemPos')) {
                item.registerdItemPos = idx * 10;
            }
        }, this);

        var regItems = Ext.ux.ItemRegistry.itemMap[this.key] || [];
        
        
        Ext.iterate(regItems, function(key, value) {
        	var addItem = this.getItem(value),
            addPos = null;
        
        	this.cmp.items.each(function(item, idx) {
                if (addItem.registerdItemPos < item.registerdItemPos) {
                    this.cmp.insert(idx, addItem);
                    addPos = idx;
                    return false;
                }
                return true;
            }, this);

            if (! Ext.isNumber(addPos)) {
                this.cmp.add(addItem);
            }
        }, this);
     },

    getItem: function(reg) {
        var def = reg.item,
            item;
            
        if (typeof def === 'function') {
            item = new def;
        } else {
            if (Ext.isString(def)) {
                def = {xtype: def};
            }
            
            item = this.cmp.lookupComponent(def);
        }

        item.registerdItemPos = reg.pos ? reg.pos : this.cmp.items.length * 10;
        
        return item;
    }
    
};
Ext.ComponentMgr.registerPlugin('ux.itemregistry', Ext.ux.ItemRegistry);

/* test
 *
Ext.onReady(function() {
    var testWin = new Ext.Window({
        width: 640,
        height: 480,
        layout: 'fit',
        title: 'ux.itemregistry test',
        items: [{
            xtype: 'tabpanel',
            activeTab: 0,
            border: false,
            defaults: {border: false},
            itemId: 'testWin',
            plugins: ['ux.itemregistry'],
            items: [{
                title: 'basepanel',
                html: 'basepanel'
            }, {
                title: 'no pos',
                html: 'no pos'
            }, {
                title: 'pos 50',
                html: 'pos 50',
                registerdItemPos: 50
            }]
        }]
    });
    testWin.show();
});

itemRegTestPanel20 = Ext.extend(Ext.Panel, {
    title: 'add panel pos 20',
    html: 'add panel pos 20',

    initComponent: function() {
        // example how to hook in owner
        this.on('added', function(me, owner, pos) {
            owner.on('tabchange', function() {
                console.log('tabchange');
            })
        }),

        itemRegTestPanel20.superclass.initComponent.call(this);
    }
});
Ext.ux.ItemRegistry.registerItem('testWin', itemRegTestPanel20, 20);

itemRegTestPanel60 = {
    xtype: 'panel',
    title: 'add panel pos 60',
    html: 'add panel pos 60'
};
Ext.ux.ItemRegistry.registerItem('testWin', itemRegTestPanel60, 60);
*/
/*
 * Tine 2.0
 * 
 * @license     New BSD License
 * @author      loeppky - based on the work done by MaximGB in Ext.ux.UploadDialog (http://extjs.com/forum/showthread.php?t=21558)
 * @version     $Id: BrowsePlugin.js 13226 2011-08-29 10:13:30Z nsendetzky $
 *
 */
Ext.ns('Ext.ux.file');

/**
 * @namespace   Ext.ux.file
 * @class       Ext.ux.file.BrowsePlugin
 * @param       {Object} config Configuration options
 */
Ext.ux.file.BrowsePlugin = function(config) {
    Ext.apply(this, config);
};

Ext.ux.file.BrowsePlugin.prototype = {
    /**
     * @cfg {Boolean} multiple
     * allow multiple files to be selected (HTML 5 only)
     */
    multiple: false,
    /**
     * @cfg {Ext.Element} dropEl 
     * element used as drop target if enableFileDrop is enabled
     */    
    dropEl: null,
    /**
     * @cfg {Boolean} enableFileDrop
     * @see http://www.w3.org/TR/2008/WD-html5-20080610/editing.html
     * 
     * enable drops from OS (defaults to true)
     */
    enableFileDrop: false,
    /**
     * @cfg {String} inputFileName
     * Name to use for the hidden input file DOM element.  Deaults to "file".
     */
    inputFileName: 'file',
    /**
     * @property inputFileEl
     * @type Ext.Element
     * Element for the hiden file input.
     * @private
     */
    input_file: null,
    /**
     * @property originalHandler
     * @type Function
     * The handler originally defined for the Ext.Button during construction using the "handler" config option.
     * We need to null out the "handler" property so that it is only called when a file is selected.
     * @private
     */
    originalHandler: null,
    /**
     * @property originalScope
     * @type Object
     * The scope originally defined for the Ext.Button during construction using the "scope" config option.
     * While the "scope" property doesn't need to be nulled, to be consistent with originalHandler, we do.
     * @private
     */
    originalScope: null,
    
    /*
     * Protected Ext.Button overrides
     */
    /**
     * @see Ext.Button.initComponent
     */
    init: function(cmp){
        this.originalHandler = cmp.handler || null;
        this.originalScope = cmp.scope || window;
        cmp.handler = null;
        cmp.scope = null;
        
        this.component = cmp;
        
        cmp.on('render', this.onRender, this);
        
        // chain fns
        if (typeof cmp.setDisabled == 'function') {
            cmp.setDisabled = cmp.setDisabled.createSequence(function(disabled) {
                if (this.input_file) {
                    this.input_file.dom.disabled = disabled;
                }
            }, this);
        }
        
        if (typeof cmp.enable == 'function') {
            cmp.enable = cmp.enable.createSequence(function() {
                if (this.input_file) {
                    this.input_file.dom.disabled = false;
                }
            }, this);
        }
        
        if (typeof cmp.disable == 'function') {
            cmp.disable = cmp.disable.createSequence(function() {
                if (this.input_file) {
                    this.input_file.dom.disabled = true;
                }
            }, this);
        }
        
        if (typeof cmp.destroy == 'function') {
            cmp.destroy = cmp.destroy.createSequence(function() {
                var input_file = this.detachInputFile(true);
                if (input_file) {
                    input_file.remove();
                }
                input_file = null;
            }, this);
        }
    },
    
    /**
     * @see Ext.Button.onRender
     */
    onRender: function() {
        this.button_container = this.buttonCt || this.component.el.child('tbody') || this.component.el;
        this.button_container.position('relative');
        this.wrap = this.component.el.wrap({cls:'tbody'});
        
        // NOTE: wrap a button in a toolbar is complex, the toolbar doLayout moves the wrap at the end
        if (this.component.ownerCt && this.component.ownerCt.el.hasClass('x-toolbar')) {
            this.component.ownerCt.on('afterlayout', function() {
                if (this.wrap.first() !== this.component.el) {
                    this.wrap.insertBefore(this.component.el);
                    this.wrap.insertFirst(this.component.el);
                }
                this.syncWrap();
            }, this);
            
            this.component.ownerCt.on('show', this.syncWrap, this);
            this.component.ownerCt.on('resize', this.syncWrap, this);
        }
        
        this.createInputFile();
        
        if (this.enableFileDrop) {
            if (! this.dropEl) {
                if (this.dropElSelector) {
                    this.dropEl = this.wrap.up(this.dropElSelector);
                } else {
                    this.dropEl = this.button_container;
                }
            }
            
            // @see http://dev.w3.org/html5/spec/Overview.html#the-dragevent-and-datatransfer-interfaces
            this.dropEl.on('dragover', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                // prevents drop in FF ;-(
                if (! Ext.isGecko) {
                    e.browserEvent.dataTransfer.dropEffect = 'copy';
                }
            }, this);
            
            this.dropEl.on('drop', function(e) {
                e.stopPropagation();
                e.preventDefault();
                var dt = e.browserEvent.dataTransfer;
                var files = dt.files;
                
                this.onInputFileChange(null, null, null, files);
            }, this);
        }
    },
    
    syncWrap: function() {
        if (this.button_container) {
            var button_size = this.button_container.getSize();
            this.wrap.setSize(button_size);
        }
    },
    
    createInputFile: function() {
        this.input_file = this.wrap.createChild(Ext.apply({
            tag: 'input',
            type: 'file',
            size: 1,
            name: this.inputFileName || Ext.id(this.component.el),
            style: "position: absolute; display: block; border: none; cursor: pointer;"
        }, this.multiple ? {multiple: true} : {}));
        
        var button_box = this.button_container.getBox();
        
        this.wrap.setBox(button_box);

        this.wrap.applyStyles('overflow: hidden; position: relative;');
        
        this.wrap.on('mousemove', function(e) {
            var xy = e.getXY();
            this.input_file.setXY([xy[0] - this.input_file.getWidth()/4, xy[1] - 10]);
        }, this);
        this.input_file.setOpacity(0.0);
        
        if (this.component.handleMouseEvents) {
            this.wrap.on('mouseover', this.component.onMouseOver || Ext.emptyFn, this.component);
            this.wrap.on('mousedown', this.component.onMouseDown || Ext.emptyFn, this.component);
            this.wrap.on('contextmenu', this.component.onContextMenu || Ext.emptyFn, this.component);
        }
        
        if(this.component.tooltip){
            if(typeof this.component.tooltip == 'object'){
                Ext.QuickTips.register(Ext.apply({target: this.input_file}, this.component.tooltip));
            } 
            else {
                this.input_file.dom[this.component.tooltipType] = this.component.tooltip;
            }
        }
        
        this.input_file.on('change', this.onInputFileChange, this);
        this.input_file.on('click', function(e) { e.stopPropagation(); });
    },
    
    /**
     * Handler when inputFileEl changes value (i.e. a new file is selected).
     * @param {FileList} files when input comes from drop...
     * @private
     */
    onInputFileChange: function(e, target, options, files){
        if (window.FileList) { // HTML5 FileList support
            this.files = files ? files : this.input_file.dom.files;
        } else {
            this.files = [{
                name : this.input_file.getValue().split(/[\/\\]/).pop()
            }];
            this.files[0].type = this.getFileCls();
        }
        
        if (this.originalHandler) {
            this.originalHandler.call(this.originalScope, this);
        }
    },
    
    /**
     * Detaches the input file associated with this BrowseButton so that it can be used for other purposed (e.g. uplaoding).
     * The returned input file has all listeners and tooltips applied to it by this class removed.
     * @param {Boolean} whether to create a new input file element for this BrowseButton after detaching.
     * True will prevent creation.  Defaults to false.
     * @return {Ext.Element} the detached input file element.
     */
    detachInputFile : function(no_create) {
        var result = this.input_file;
        
        no_create = no_create || false;
        
        if (this.input_file) {
            if (typeof this.component.tooltip == 'object') {
                Ext.QuickTips.unregister(this.input_file);
            }
            else {
                this.input_file.dom[this.component.tooltipType] = null;
            }
            this.input_file.removeAllListeners();
        }
        this.input_file = null;
        
        if (!no_create) {
            this.createInputFile();
        }
        return result;
    },
    
    getFileList: function() {
        return this.files;
    },
    
    /**
     * @return {Ext.Element} the input file element
     */
    getInputFile: function(){
        return this.input_file;
    },
    /**
     * get file name
     * @return {String}
     */
    getFileName:function() {
        var file = this.getFileList()[0];
        return file.name ? file.name : file.fileName;
    },
    
    /**
     * returns file class based on name extension
     * @return {String} class to use for file type icon
     */
    getFileCls: function() {
        var fparts = this.getFileName().split('.');
        if(fparts.length === 1) {
            return '';
        }
        else {
            return fparts.pop().toLowerCase();
        }
    },
    isImage: function() {
        var cls = this.getFileCls();
        return (cls == 'jpg' || cls == 'gif' || cls == 'png' || cls == 'jpeg');
    }
};/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: Downloader.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.file');

/**
 * @namespace   Ext.ux.file
 * @class       Ext.ux.file.Downloader
 * @extends     Ext.util.Observable
 */
Ext.ux.file.Downloader = function(config) {
    config = config || {};
    Ext.apply(this, config);
    
    Ext.ux.file.Downloader.superclass.constructor.call(this);
    
    this.addEvents({
        'success': true,
        'fail': true,
        'abort': true
    });
};

Ext.extend(Ext.ux.file.Downloader, Ext.util.Observable, {    
    url: null,
    method: 'POST',
    params: null,
    timeout: 1800000, // 30 minutes
    
    /**
     * @private 
     */
    form: null,
    transactionId: null,
    
    /**
     * start download
     */
    start: function() {
        this.form = Ext.getBody().createChild({
            tag:'form',
            method: this.method,
            cls:'x-hidden'
        });

        var con = new Ext.data.Connection({
            // firefox specific problem -> see http://www.extjs.com/forum/archive/index.php/t-44862.html
            //  "It appears that this is because the "load" is completing once the initial download dialog is displayed, 
            //  but the frame is then destroyed before the "save as" dialog is shown."
            //
            // TODO check if we can handle firefox event 'onSaveAsSubmit' (or something like that)
            //
            debugUploads: Ext.isGecko
        });
        
        this.transactionId = con.request({
            isUpload: true,
            form: this.form,
            params: this.params,
            scope: this,
            success: this.onSuccess,
            failure: this.onFailure,
            url: this.url,
            timeout: this.timeout
        });
    },
    
    /**
     * abort download
     */
    abort: function() {
        Ext.Ajax.abort(this.transactionId);
        this.form.remove();
        this.fireEvent('abort', this);
    },
    
    /**
     * @private
     * 
     */
    onSuccess: function() {
        this.form.remove();
        this.fireEvent('success', this);
    },
    
    /**
     * @private
     * 
     */
    onFailure: function() {
        this.form.remove();
        this.fireEvent('fail', this);
    }
    
});
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: Uploader.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.file');

/**
 * a simple file uploader
 * objects of this class represent a single file uplaod
 * 
 * @namespace   Ext.ux.file
 * @class       Ext.ux.file.Uploader
 * @extends     Ext.util.Observable
 * @autor       Cornelius Weiss <c.weiss@metaways.de>
 * @license     BSD, MIT and GPL
 * @verstion    $Id: Uploader.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */
Ext.ux.file.Uploader = function(config) {
    Ext.apply(this, config);

    Ext.ux.file.Uploader.superclass.constructor.apply(this, arguments);
    
    this.addEvents(
        /**
         * @event uploadcomplete
         * Fires when the upload was done successfully 
         * @param {Ext.ux.file.Uploader} this
         * @param {Ext.Record} Ext.ux.file.Uploader.file
         * @param {Object} raw response
         */
         'uploadcomplete',
        /**
         * @event uploadfailure
         * Fires when the upload failed 
         * @param {Ext.ux.file.Uploader} this
         * @param {Ext.Record} Ext.ux.file.Uploader.file
         */
         'uploadfailure',
        /**
         * @event uploadprogress
         * Fires on upload progress (html5 only)
         * @param {Ext.ux.file.Uploader} this
         * @param {Ext.Record} Ext.ux.file.Uploader.file
         * @param {XMLHttpRequestProgressEvent}
         */
         'uploadprogress' );
};
 
Ext.extend(Ext.ux.file.Uploader, Ext.util.Observable, {
    
    /**
     * @cfg {String} url the url we upload to
     */
    url: 'index.php',
    
    /**
     * @cfg {String} methodName name of method to call for uploads
     */
    methodName: '',
    
    /**
     * @cfg {String} methodParam server side method param (defaults to 'method')
     */
    methodParam: 'method',
    
    /**
     * @cfg {Number} timeout
     */
    timeout: 30000,
    
    /**
     * @cfg {Boolean} allowHTML5Uploads
     */
    allowHTML5Uploads: true,
    
    /**
     * @cfg {Object} HTML4params additional request params for html4 uploads
     */
    HTML4params: null,
    
    /**
     * @cfg {Int} maxFileSize the maximum file size in bytes
     */
    maxFileSize: 20971520, // 20 MB
    
    /**
     * @cfg {Ext.ux.file.BrowsePlugin} fileSelector
     * a file selector
     */
    fileSelector: null,
    
    /**
     * creates a form where the upload takes place in
     * @private
     */
    createForm: function() {
        var form = Ext.getBody().createChild({
            tag:'form',
            action:this.url,
            method:'post',
            cls:'x-hidden',
            id:Ext.id(),
            cn:[{
                tag: 'input',
                type: 'hidden',
                name: 'MAX_FILE_SIZE',
                value: this.maxFileSize
            }]
        });
        return form;
    },
    
    /**
     * perform the upload
     * 
     * @param  {FILE} file to upload (optional for html5 uploads)
     * @return {Ext.Record} Ext.ux.file.Uploader.file
     */
    upload: function(file) {
        if (this.allowHTML5Uploads && (
            (! Ext.isGecko && window.XMLHttpRequest && window.File && window.FileList) || // safari, chrome, ...?
            (Ext.isGecko && window.FileReader) ) && file) {
            return this.html5upload(file);
        } else {
            return this.html4upload();
        }
    },
    
    /**
     * 2010-01-26 Current Browsers implemetation state of:
     *  http://www.w3.org/TR/FileAPI
     *  
     *  Interface: File | Blob | FileReader | FileReaderSync | FileError
     *  FF       : yes  | no   | no         | no             | no       
     *  safari   : yes  | no   | no         | no             | no       
     *  chrome   : yes  | no   | no         | no             | no       
     *  
     *  => no json rpc style upload possible
     *  => no chunked uploads posible
     *  
     *  But all of them implement XMLHttpRequest Level 2:
     *   http://www.w3.org/TR/XMLHttpRequest2/
     *  => the only way of uploading is using the XMLHttpRequest Level 2.
     */
    html5upload: function(file) {
        var fileRecord = new Ext.ux.file.Uploader.file({
            name: file.name ? file.name : file.fileName,  // safari and chrome use the non std. fileX props
            type: (file.type ? file.type : file.fileType) || this.fileSelector.getFileCls(), // missing if safari and chrome
            size: (file.size ? file.size : file.fileSize) || 0, // non standard but all have it ;-)
            status: 'uploading',
            progress: 0,
            input: this.getInput()
        });
        
        var conn = new Ext.data.Connection({
            disableCaching: true,
            method: 'POST',
            url: this.url + '?' + this.methodParam + '=' + this.methodName,
            timeout: this.timeout,
            defaultHeaders: {
                "Content-Type"          : "application/x-www-form-urlencoded",
                "X-Requested-With"      : "XMLHttpRequest"
            }
        });
        
        var transaction = conn.request({
            headers: {
                "X-File-Name"           : fileRecord.get('name'),
                "X-File-Type"           : fileRecord.get('type'),
                "X-File-Size"           : fileRecord.get('size')
            },
            xmlData: file,
            success: this.onUploadSuccess.createDelegate(this, [fileRecord], true),
            failure: this.onUploadFail.createDelegate(this, [fileRecord], true),
            fileRecord: fileRecord
        });
        
        var upload = transaction.conn.upload;
        
        upload['onprogress'] = this.onUploadProgress.createDelegate(this, [fileRecord], true);
        
        return fileRecord;
    },
    
    /**
     * uploads in a html4 fashion
     * 
     * @return {Ext.data.Connection}
     */
    html4upload: function() {
        var form = this.createForm();
        var input = this.getInput();
        form.appendChild(input);
        
        var fileRecord = new Ext.ux.file.Uploader.file({
            name: this.fileSelector.getFileName(),
            size: 0,
            type: this.fileSelector.getFileCls(),
            input: input,
            form: form,
            status: 'uploading',
            progress: 0
        });
        
        var params = {};
        params[this.methodParam] = this.methodName;
        Ext.apply(params, this.HTML4params);
        
        Ext.Ajax.request({
            fileRecord: fileRecord,
            isUpload: true,
            method:'post',
            form: form,
            timeout: this.timeout,
            success: this.onUploadSuccess.createDelegate(this, [fileRecord], true),
            failure: this.onUploadFail.createDelegate(this, [fileRecord], true),
            params: params
        });
        
        return fileRecord;
    },
    
    /*
    onLoadStart: function(e, fileRecord) {
        this.fireEvent('loadstart', this, fileRecord, e);
    },
    */
    
    onUploadProgress: function(e, fileRecord) {
        var percent = Math.round(e.loaded / e.total * 100);
        fileRecord.set('progress', percent);
        this.fireEvent('uploadprogress', this, fileRecord, e);
    },
    
    /**
     * executed if a file got uploaded successfully
     */
    onUploadSuccess: function(response, options, fileRecord) {
        try {
            response = Ext.util.JSON.decode(response.responseText);
        } catch (e) {
            return this.onUploadFail(response, options, fileRecord);
        }
        
        if (response.status && response.status !== 'success') {
            this.onUploadFail(response, options, fileRecord);
        } else {
            fileRecord.beginEdit();
            fileRecord.set('status', 'complete');
            fileRecord.set('tempFile', response.tempFile);
            if (response.tempFile) {
                fileRecord.set('name', response.tempFile.name);
                fileRecord.set('size', response.tempFile.size);
                fileRecord.set('type', response.tempFile.type);
                fileRecord.set('path', response.tempFile.path);
            }
            fileRecord.commit(false);
            
            this.fireEvent('uploadcomplete', this, fileRecord, response);
        }
        
        /** @todo Is this correct? */
        return true;
    },
    
    /**
     * executed if a file upload failed
     */
    onUploadFail: function(response, options, fileRecord) {
        fileRecord.set('status', 'failure');
        
        this.fireEvent('uploadfailure', this, fileRecord);
    },
    
    // private
    getInput: function() {
        if (! this.input) {
            this.input = this.fileSelector.detachInputFile();
        }
        
        return this.input;
    }
});

Ext.ux.file.Uploader.file = Ext.data.Record.create([
    {name: 'id', type: 'text', system: true},
    {name: 'name', type: 'text', system: true},
    {name: 'size', type: 'number', system: true},
    {name: 'type', type: 'text', system: true},
    {name: 'status', type: 'text', system: true},
    {name: 'progress', type: 'number', system: true},
    {name: 'form', system: true},
    {name: 'input', system: true},
    {name: 'request', system: true},
    {name: 'path', system: true},
    {name: 'tempFile', system: true}
]);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: DecimalField.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.form');

Ext.ux.form.DecimalField = Ext.extend(Ext.form.NumberField, {

	setValue : function(v) {

		v = Ext.isNumber(v) ? v : parseFloat(String(v).replace( this.decimalSeparator, "."));

		if (isNaN(v)) {
			v = 0.0;
		}

		v= v.toFixed(this.decimalPrecision).replace(".", this.decimalSeparator);

		return Ext.form.NumberField.superclass.setValue.call(this, v);
	}
});

Ext.reg('ux.decimalfield', Ext.ux.form.DecimalField);
/**
 * A ComboBox with a secondary trigger button that clears the contents of the ComboBox
 * 
 * @namespace   Ext.ux.form
 * @class       Ext.ux.form.ClearableComboBox
 * @extends     Ext.form.ComboBox
 * @autor       Cornelius Weiss <c.weiss@metaways.de>
 * @license     BSD, MIT and GPL
 * @version    $Id: ClearableComboBox.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.form');

Ext.ux.form.ClearableComboBox = Ext.extend(Ext.form.ComboBox, {
    initComponent : function(){
        Ext.ux.form.ClearableComboBox.superclass.initComponent.call(this);

        this.triggerConfig = {
            tag:'span', cls:'x-form-twin-triggers', style:'padding-right:2px',  // padding needed to prevent IE from clipping 2nd trigger button
            cn:[
                {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger x-form-clear-trigger"},            
                {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger"}                            
            ]
        };
    },

    getTrigger : function(index){
        return this.triggers[index];
    },

    initTrigger : function(){
        var ts = this.trigger.select('.x-form-trigger', true);
        this.wrap.setStyle('overflow', 'hidden');
        var triggerField = this;
        ts.each(function(t, all, index){
            t.hide = function(){
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = 'none';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            t.show = function(){
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = '';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            var triggerIndex = 'Trigger'+(index+1);

            if(this['hide'+triggerIndex]){
                t.dom.style.display = 'none';
            }
            t.on("click", this['on'+triggerIndex+'Click'], this, {preventDefault:true});
            t.addClassOnOver('x-form-trigger-over');
            t.addClassOnClick('x-form-trigger-click');
        }, this);
        this.triggers = ts.elements;
        this.triggers[0].hide();                   
    },
    
    // clear contents of combobox
    onTrigger1Click : function() {
        this.clearValue();
        this.fireEvent('select', this, this.getRawValue(), this.startValue);
        this.startValue = this.getRawValue();
        this.triggers[0].hide();
    },
    // pass to original combobox trigger handler
    onTrigger2Click : function() {
        this.onTriggerClick();
    },
    // show clear triger when item got selected
    onSelect: function(combo, record, index) {
        Ext.ux.form.ClearableComboBox.superclass.onSelect.apply(this, arguments);
        this.startValue = this.getValue();
        this.triggers[0].show();
    },
    
    setValue: function(value) {
        Ext.ux.form.ClearableComboBox.superclass.setValue.call(this, value);
        if (value && this.rendered) {
            this.triggers[0].show();
        }
    }
});
Ext.reg('ux.clearablecombo', Ext.ux.form.ClearableComboBox);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: DecimalField.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.form');

Ext.ux.form.FormattableDisplayField = Ext.extend( Ext.form.DisplayField, {

	constructor: function( config ) {

		var conf = config || {};

		Ext.applyIf( conf, {
			renderer: null
		} );

		Ext.ux.form.FormattableDisplayField.superclass.constructor.call( this, conf );
	},


	setValue : function( v ) {

		if( this.renderer ) {
			v = this.renderer( v );
		}

		return Ext.ux.form.FormattableDisplayField.superclass.setValue.call( this, v );
	}
} );

Ext.reg( 'ux.formattabledisplayfield', Ext.ux.form.FormattableDisplayField );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * don't evaluate to dot (.) as obj seperator when creating accessors, 
 * to support dot's in fieldnames
 */
Ext.apply(Ext.data.JsonReader.prototype, {
    createAccessor : function(){
        var re = /[\[]/;
        return function(expr) {
            if(Ext.isEmpty(expr)){
                return Ext.emptyFn;
            }
            if(Ext.isFunction(expr)){
                return expr;
            }
            var i = String(expr).search(re);
            if(i >= 0){
                return new Function('obj', 'return obj' + (i > 0 ? '.' : '') + expr);
            }
            return function(obj){
                return obj[expr];
            };

        };
    }()
});

/**
 * - support dot's in fieldnames
 * - generic status renderer
 */
Ext.form.ComboBox.prototype.initList = Ext.form.ComboBox.prototype.initList.createInterceptor(function() {
    // autodetect status fieldname
    if (! this.statusField) {
        this.store.fields.each(function(field) {
            if (field.name.match(/\.status$/)) {
                this.statusField = field.name;
            }
        }, this);
    }

    this.tpl = '<tpl for="."><div class="x-combo-list-item statustext-{[values["' + this.statusField + '"]]}">{[values["' + this.displayField + '"]]}</div></tpl>';
});


Ext.util.JSON.encodeDate = function( o ) {

	pad = function( n ) {
		return n < 10 ? "0" + n : n;
	};

	return '"' + o.getFullYear() + "-" +
    	pad(o.getMonth() + 1) + "-" +
    	pad(o.getDate()) + " " +
    	pad(o.getHours()) + ":" +
    	pad(o.getMinutes()) + ":" +
    	pad(o.getSeconds()) + '"';
};
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop');

/**
 * @singleton
 */
MShop.Schema = {
    recordCache: {},
    filterModelCache: {},
    
    schemaMap: {},
    searchSchemaMap: {},
    
    getRecord: function (schemaName) {
        if (! this.recordCache.hasOwnProperty(schemaName)) {
            var fields = [],
                schema = this.getSchema(schemaName);
            
            for (var fieldName in schema.properties) {
                
                fields.push({
                    name: fieldName,
                    type: this.getType(schema.properties[fieldName]),
                    dateFormat: 'Y-m-d H:i:s'
                });
            }
            
            this.recordCache[schemaName] = Ext.data.Record.create(fields);
        }
        
        return this.recordCache[schemaName];
    },
    
    getFilterModel: function(schemaName) {
        if (! this.filterModelCache.hasOwnProperty(schemaName)) {
            var fields = [],
                schema = this.getSearchSchema(schemaName);
                
            for (var dataIndex in schema.criteria) {
                fields.push({
                    label: schema.criteria[dataIndex].description,
                    dataIndex: dataIndex,
                    xtype: this.getFilterXType(schema.criteria[dataIndex])
                });
            }
            
            this.filterModelCache[schemaName] = fields;
        }
        
        return this.filterModelCache[schemaName];
    },
    
    getFilterXType: function(criteriaModel) {
        switch (criteriaModel.type) {
            case 'string': return 'ux.textfilter';
            case 'boolean': return 'ux.booleanfilter';
            case 'integer': return 'ux.numberfilter';
            case 'decimal': return 'ux.numberfilter';
            case 'datetime': return 'ux.datefilter';
            default: return 'ux.textfilter';
        }
    },
    
    getType: function (field) {
        switch (field.type) {
            case 'datetime': return 'date';
            case 'integer': return 'auto'; // we convert to auto to support NULL's
            default: return 'auto';
        }
    },
    
    getSchema: function (schemaName) {
    	if (! this.schemaMap.hasOwnProperty(schemaName)) {
            throw new Ext.Error('schema "' + schemaName + '" is  not registered');
        }
        
        return this.schemaMap[schemaName];
    },
    
    getSearchSchema: function (schemaName) {
        if (! this.searchSchemaMap.hasOwnProperty(schemaName)) {
            throw new Ext.Error('search schema "' + schemaName + '" is  not registered');
        }
        
        return this.searchSchemaMap[schemaName];
    },
    
    // MShop specific
    register: function(itemschema, searchschema) {
        this.schemaMap = itemschema;
        this.searchSchemaMap = searchschema;
    }
    
};/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */

Ext.ns('MShop');

MShop.Config = {
	
	configuration: {},
	

	get: function( name, defaultName ) {
		name = name.replace(/^\/|\/$/g, ''); //trim '/'
		var parts = name.split('/');
		
		if( ( value = this._get( this.configuration, parts ) ) !== null ) {
			return value;
		}
				
		return defaultName;
	},
	
	init : function( configuration ) {
		this.configuration = JSON.parse(configuration);
	},
	
	set: function( name, value ) {
		name = name.replace(/^\/|\/$/g, ''); //trim '/'
		var parts = name.split('/');
		
		this.configuration = this._set( this.configuration, parts, value );
	},
	
	_get: function( config, parts )
	{
		if( ( current = parts.shift() ) !== undefined && config[current] !== undefined )
		{
			if( parts.length > 0 ) {
				return this._get( config[current], parts );
			}

			return config[current];
		}

		return null;
	},
	
	_set: function( config, path, value )
	{
		var current = path.shift();
		
		if( current !== undefined )
		{
			if( config[current] !== undefined ) {
				config[current] = this._set( config[current], path, value );
			} else {
				config[current] = this._set( new Object(), path, value );
			}

			return config;
		}

		return value;
	}
};
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements');

MShop.elements.PagingToolbar = function(config) {
	Ext.applyIf(config, {
		/**
		 * @cfg {Number} page size (defaults to 50)
		 */
		pageSize: 50
	});

	MShop.elements.PagingToolbar.superclass.constructor.call(this, config);
};


/**
 * MShop paging adoption
 * - set default page size
 * - removes the extjs need to define paging in the stores initial load
 * 
 * @namespace   MShop 
 * @class       MShop.elements.PagingToolbar
 * @extends     Ext.PagingToolbar
 */
 Ext.extend(MShop.elements.PagingToolbar, Ext.PagingToolbar, {

	// private
	beforeLoad : function(store, options){
		options.params = options.params || {};
		var o = options.params, pn = this.getParams();
		o[pn.start] = o.hasOwnProperty(pn.start) ? o[pn.start] : 0;
		o[pn.limit] = o.hasOwnProperty(pn.limit) ? o[pn.limit] : this.pageSize;
		
		return MShop.elements.PagingToolbar.superclass.beforeLoad.apply(this, arguments);
	},

	// private
	doLoad : function(start){
		var o = {}, pn = this.getParams();
		o[pn.start] = start;
		o[pn.limit] = this.pageSize;
		if(this.fireEvent('beforechange', this, o) !== false){
			this.nextCursor = start;
			this.store.load({params:o});
		}
	},

	// private
	onLoad : function(store, r, o){
		var pn = this.getParams();

		o.params = o.params || {};
		o.params[pn.start] = o.params.hasOwnProperty(pn.start) ? o.params[pn.start] : this.nextCursor || 0;

		MShop.elements.PagingToolbar.superclass.onLoad.apply(this, arguments);
	}
});

Ext.reg('MShop.elements.pagingtoolbar', MShop.elements.PagingToolbar);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop');

MShop.UrlManager = function( href ) {
		this.href = href || null;
		this.tmpl = new Ext.Template( MShop.config.urlTemplate );
		this.data = {
			site: MShop.config.site['locale.site.code'],
			tab : MShop.config.activeTab
		};
};

MShop.UrlManager.prototype = {
		redirect: function( config )
		{
			if( typeof config == 'object' )
			{
				if( config.hasOwnProperty( 'site' ) ) {
					this.setSiteCode( config.site );
				}

				if( config.hasOwnProperty( 'tab' ) ) {
					this.setActiveTab( config.tab );
				}
			}
			window.location.href = this.tmpl.apply( this.data );
		},

		setActiveTab: function( value ) {
			this.data.tab = parseInt( value, 10 );
		},

		getActiveTab: function() {
			return this.data.tab;
		},

		setSiteCode: function( siteCode ) {
			this.data.site = siteCode;
		}
};
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


/* superglobal lang stubs */
_ = function(m) {return m; };
n_ = function(s,p,n) {return ( n > 1 ? p : s ); };

Ext.onReady(function() {

	Ext.ns('MShop.API');
    
    // init jsonSMD
    Ext.Direct.addProvider(Ext.apply(MShop.config.smd, {
        'type'              : 'jsonrpcprovider',
        'namespace'         : 'MShop.API',
        'url'               : MShop.config.smd.target,
        'useNamedParams'    : true
    }));
    
	// init schemas
	MShop.Schema.register(MShop.config.itemschema, MShop.config.searchschema);
    
	//init configs
	MShop.Config.init(MShop.config.configuration);
    
    // no endswith textfilters operators
    Ext.ux.AdvancedSearch.TextFilter.prototype.operators = ['equals', 'contains', 'startswith'];
    
    MShop.urlManager = new MShop.UrlManager( window.location.href );
    
    // build interface
    new Ext.Viewport({
        layout: 'fit',
        items: [{
            layout: 'fit',
            border: false,
            tbar: ['->', /*_('Site:'),*/ {xtype: 'MShop.elements.site.combo'}],
            items: [{
                xtype: 'tabpanel',
                border: false,
                activeTab: MShop.urlManager.getActiveTab(),
                id: 'MShop.MainTabPanel',
                itemId: 'MShop.MainTabPanel',
                plugins: ['ux.itemregistry']
            }]
        }]
    });
});
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.site');

MShop.elements.site.ComboBox = function(config) {
	Ext.applyIf(config, {
		recordName: 'Locale_Site',
		idProperty: 'locale.site.id',
		displayField: 'locale.site.label',
		valueField: 'locale.site.code',
		forceSelection: true,
		triggerAction: 'all',
		typeAhead: true,
		width: 250,
		pageSize: 20
	});

	MShop.elements.site.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.site.ComboBox, Ext.form.ComboBox, {

	initComponent: function() {
		this.store = MShop.elements.site.getStore();
		this.on('select', this.onSiteSelect, this);
		MShop.elements.site.ComboBox.superclass.initComponent.call(this);
		this.setValue(MShop.config.site["locale.site.label"]);
	},

	onSiteSelect: function(ComboBox, site) {
		var mainTabPanel = Ext.getCmp('MShop.MainTabPanel'),
			activeTabPanel = mainTabPanel.getActiveTab(),
			domainTabIdx = mainTabPanel.items.indexOf(activeTabPanel),
			siteCode = site ? site.get('locale.site.code') : 'default';

		new Ext.LoadMask(Ext.getBody(), {msg: _('Switching Site...')}).show();

		MShop.urlManager.redirect( { site: siteCode, tab : domainTabIdx } );
	}
});

Ext.reg('MShop.elements.site.combo', MShop.elements.site.ComboBox);


/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.site.getStore = function() {
	if (! MShop.elements.site._store) {
		MShop.elements.site._store = MShop.GlobalStoreMgr.createStore('Locale_Site', {
			remoteSort : true,
			sortInfo: {
				field: 'locale.site.label',
				direction: 'ASC'
			}
		});
	}

	return MShop.elements.site._store;
};


//preload
Ext.onReady(function() { MShop.elements.site.getStore().load(); });
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop');

/**
 * a store with remote data loaded at first usage
 * 
 * @singelton
 * @class       MShop.GlobalStore
 */
MShop.GlobalStoreMgr = {
    stores: {},
    
    /**
     * get store for given recordName
     * 
     * @param {String} recordName
     * @param {String} domain (optionl)
     * @param {Object} storeConfig (optional)
     * @return {Ext.data.DirectStore}
     */
    get: function(recordName, domain, storeConfig) {
        domain = domain || '__NODOMAIN__';
        this.stores[domain] = this.stores[domain] || {};
        
        if (! this.stores[domain][recordName]) {
            this.stores[domain][recordName] = this.createStore(recordName, storeConfig);
            
            this.stores[domain][recordName].load();
        }
        
        return this.stores[domain][recordName];
    },
    
    createStore: function(recordName, storeConfig) {
        storeConfig = storeConfig || {};
        
        // autodetect idProperty
        if (! storeConfig.idProperty) {
            storeConfig.idProperty = recordName.toLowerCase().replace(/_/g, '.') + '.id';
        }
        
        var store = new Ext.data.DirectStore(Ext.apply({
            autoLoad: false,
            remoteSort : false,
            hasMultiSort: true,
            fields: MShop.Schema.getRecord(recordName),
            api: {
                read    : MShop.API[recordName].searchItems,
                create  : MShop.API[recordName].saveItems,
                update  : MShop.API[recordName].saveItems,
                destroy : MShop.API[recordName].deleteItems
            },
            writer: new Ext.data.JsonWriter({
                writeAllFields: true,
                encode: false
            }),
            paramsAsHash: true,
            root: 'items',
            totalProperty: 'total',
            baseParams: {
                site: MShop.config.site["locale.site.code"]
            }
        }, storeConfig));
        
        return store;
    }
};

/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.language');

MShop.elements.language.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Language'),
        anchor: '100%',
        store: MShop.elements.language.getStore(),
        mode: 'local',
        displayField: 'locale.language.label',
        valueField: 'locale.language.id',
        statusField: 'locale.language.status',
        triggerAction: 'all',
        pageSize: 20,
        emptyText: _('all'),
        typeAhead: true
    });
    
    MShop.elements.language.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.language.ComboBox, Ext.ux.form.ClearableComboBox);

Ext.reg('MShop.elements.language.combo', MShop.elements.language.ComboBox);


/**
 * @static
 * 
 * @param {String} langId
 * @return {String} label
 */
MShop.elements.language.renderer = function(langId, metaData, record, rowIndex, colIndex, store) {
	var lang = MShop.elements.language.getStore().getById(langId);

    metaData.css = 'statustext-' + ( lang ? Number( lang.get('locale.language.status') ) : '1' );
    
    return langId || _('all');
};

/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.language.getStore = function() {
    if (! MShop.elements.language._store) {
        MShop.elements.language._store = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.language.status',
                direction: 'DESC'
            }
        });
    }
    
    return MShop.elements.language._store;
};

// preload
Ext.onReady(function() { MShop.elements.language.getStore().load(); });
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.currency');

MShop.elements.currency.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Currency'),
        anchor: '100%',
        store: MShop.elements.currency.getStore(),
        mode: 'local',
        displayField: 'locale.currency.label',
        valueField: 'locale.currency.id',
        statusField: 'locale.currency.status',
        triggerAction: 'all',
        pageSize: 20,
        typeAhead: true,
        allowBlank: false
    });
    
    MShop.elements.currency.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.currency.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.currency.combo', MShop.elements.currency.ComboBox);


/**
 * @static
 * 
 * @param {String} langId
 * @return {String} label
 */
MShop.elements.currency.renderer = function(currencyId, metaData, record, rowIndex, colIndex, store) {

	var currency = MShop.elements.currency.getStore().getById(currencyId);
    
    metaData.css = 'statustext-' + ( currency ? Number( currency.get( 'locale.currency.status' ) ) : '1' );
    
    return currencyId;
};


/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.currency.getStore = function() {
    if (! MShop.elements.currency._store) {
        MShop.elements.currency._store = MShop.GlobalStoreMgr.createStore('Locale_Currency', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.currency.status',
                direction: 'DESC'
            }
        });
    }
    
    return MShop.elements.currency._store;
};

// preload
Ext.onReady(function() { MShop.elements.currency.getStore().load(); });
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.status');

MShop.elements.status.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Status'),
        anchor: '100%',
        store: MShop.elements.status._store,
        mode: 'local',
        displayField: 'label',
        valueField: 'value',
        triggerAction: 'all',
        typeAhead: true,
		value: 1
    });

    MShop.elements.status.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.status.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.status.combo', MShop.elements.status.ComboBox);


/**
 * @static
 *
 * @return {Ext.data.DirectStore}
 */
MShop.elements.status._store = new Ext.data.ArrayStore({
    idIndex : 0,
    fields : [
       {name: 'value', type: 'integer'},
       {name: 'label', type: 'string'}
    ],
    data : [
    	[-2, _('archive')],
    	[-1, _('review')],
    	[0, _('disabled')],
    	[1, _('enabled')]
    ]
});

/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.domain');

MShop.elements.domain.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Domain'),
        anchor: '100%',
        store: MShop.elements.domain._store,
        mode: 'local',
        displayField: 'label',
        emptyText : _('Domain (required)'),
        valueField: 'value',
        triggerAction: 'all',
        typeAhead: true
    });

    MShop.elements.domain.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.domain.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.domain.combo', MShop.elements.domain.ComboBox);

MShop.elements.domain.renderer = function(id, metaData, record, rowIndex, colIndex, store) {

    var value = MShop.elements.domain._store.getById(id);

    if( value ) {
    	metaData.css = 'text_type_' + value.get('value');
    	return label;
    }

    return value;
};

/**
 * @static
 *
 * @return {Ext.data.DirectStore}
 */
MShop.elements.domain._store = new Ext.data.ArrayStore({
    idIndex : 0,
    fields : [
       {name: 'value', type: 'string'},
       {name: 'label', type: 'string'}
    ],
    data : [
  		['attribute', _('Attribute')],
    	['product', _('Product')],
    	['media', _('Media')],
    	['catalog', _('Catalog')],
    	['service', _('Service')],
    	['plugin', _('Plugin')],
    	['customer', _('Customer')],
    	['supplier', _('Supplier')],
    	['text', _('Text')],
    	['price', _('Price')]
    ]
});/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.AbstractListUi = Ext.extend(Ext.Panel, {
	/**
	 * @cfg {String} recordName (required)
	 */
	recordName: null,

	/**
	 * @cfg {String} idProperty (required)
	 */
	idProperty: null,

	/**
	 * @cfg {String} siteidProperty (required)
	 */
	siteidProperty: null,

	/**
	 * @cfg {String} exportMethod (required)
	 */
	exportMethod: null,

	/**
	 * @cfg {String} domain (optional)
	 */
	domain: null,

	/**
	 * @cfg {String} domainProperty (optional)
	 */
	domainProperty: null,

	/**
	 * @cfg {Object} sortInfo (optional)
	 */
	sortInfo: null,

	/**
	 * @cfg {String} autoExpandColumn (optional)
	 */
	autoExpandColumn: null,

	/**
	 * @cfg {Object} storeConfig (optional)
	 */
	storeConfig: null,

	/**
	 * @cfg {Object} gridConfig (optional)
	 */
	gridConfig: null,

	/**
	 * @cfg {Object} filterConfig (optional)
	 */
	filterConfig: null,

	/**
	 * @cfg {String} itemUi xtype
	 */
	itemUi: '',

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',


	/**
	 * @cfg {Object} importMethod (optional)
	 */
	importMethod: null,

	border: false,
	layout: 'fit',

	initComponent: function() {
		this.initActions();
		this.initToolbar();
		this.initStore();

		if (this.filterConfig) {
			this.filterConfig.filterModel = this.filterConfig.filterModel || MShop.Schema.getFilterModel(this.recordName);
		}

		this.grid = new Ext.grid.GridPanel(Ext.apply({
			border: false,
			store: this.store,
			loadMask: true,
			autoExpandColumn: this.autoExpandColumn,
			columns: this.getColumns(),
			tbar: Ext.apply({
				xtype: 'ux.advancedsearch',
				store: this.store
			}, this.filterConfig),
			bbar: {
				xtype: 'MShop.elements.pagingtoolbar',
				store: this.store
			}
		}, this.gridConfig));

		this.items = [this.grid];

		this.grid.on('rowcontextmenu', this.onGridContextMenu, this);
		this.grid.on('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);
		this.grid.getSelectionModel().on('selectionchange', this.onGridSelectionChange, this, {buffer: 10});
		
		MShop.panel.AbstractListUi.superclass.initComponent.apply(this, arguments);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: _( 'No Items' ),
				getRowClass: function( record, index ) {

					siteid = MShop.config.site['locale.site.id'];
					if( record.phantom === false && record.get( this.siteidProperty ) != siteid ) {
						return this.rowCssClass;
					}
					return '';

				}.createDelegate(this)
			}
		});
	},

	initActions: function() {
		this.actionAdd = new Ext.Action({
			text: _('Add'),
			handler: this.onOpenEditWindow.createDelegate(this, ['add'])
		});

		this.actionEdit = new Ext.Action({
			text: _('Edit'),
			disabled: true,
			handler: this.onOpenEditWindow.createDelegate(this, ['edit'])
		});
		
		this.actionCopy = new Ext.Action({
			text: _('Copy'),
			disabled: true,
			handler: this.onOpenEditWindow.createDelegate(this, ['copy'])
		});

		this.actionDelete = new Ext.Action({
			text: _('Delete'),
			disabled: true,
			handler: this.onDeleteSelectedItems.createDelegate(this)
		});

		this.actionExport = new Ext.Action({
			text: _('Export'),
			disabled: false,
			handler: this.onExport ? this.onExport.createDelegate(this) : Ext.emptyFn
		});

		this.importButton = new MShop.elements.ImportButton({
			importMethod: this.importMethod,
			text: _('Import'),
			disabled: (this.importMethod === null)
		});

	},

	initToolbar: function() {
		this.tbar = [
			this.actionAdd,
			this.actionEdit,
			this.actionCopy,
			this.actionDelete,
			this.actionExport,
			this.importButton
		];
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read    : MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	},

	afterRender: function() {
		MShop.panel.AbstractListUi.superclass.afterRender.apply(this, arguments);

		if (! this.store.autoLoad) {
			this.store.load.defer(50, this.store);
		}
	},

	getCtxMenu: function() {
		if (! this.ctxMenu) {
			this.ctxMenu = new Ext.menu.Menu({
				items: [
					this.actionAdd,
					this.actionEdit,
					this.actionCopy,
					this.actionDelete,
					this.actionExport
				]
			});
		}

		return this.ctxMenu;
	},

	onBeforeLoad: function(store, options) {
		this.setSiteParam(store);
		
		if (this.domain) {
			this.setDomainFilter(store, options);
		}

		this.actionExport.setDisabled(this.exportMethod === null);
	},

	onBeforeWrite: function(store, action, records, options) {
		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainProperty(store, action, records, options);
		}
	},

	onDeleteSelectedItems: function() {
		var that = this;

		Ext.Msg.show({
			title: _('Delete items?'),
			msg: _('You are going to delete one or more items. Would you like to proceed?'),
			buttons: Ext.Msg.YESNO,
			fn: function (btn) {
				if (btn == 'yes') {
					that.store.remove(that.grid.getSelectionModel().getSelections());
				}
			},
			animEl: 'elId',
			icon: Ext.MessageBox.QUESTION
		});
	},

	/**
	 * start download
	 */
	onExport: function() {
		var win = new MShop.elements.exportlanguage.Window();
		win.on('save', this.finishExport, this);
		win.show();
	},

	finishExport: function(langwin, languages) {
		var selection = this.grid.getSelectionModel().getSelections(),
		ids = [];

		Ext.each(selection, function(r){
			ids.push(r.id);
		}, this);

		var downloader = new Ext.ux.file.Downloader({
			url: MShop.config.smd.target,
			params: {
				method: this.exportMethod,
				params: Ext.encode({
					items: ids,
					lang: languages,
					site: MShop.config.site['locale.site.code']
				})
			}
		}).start();
	},

	onDestroy: function() {
		this.grid.un('rowcontextmenu', this.onGridContextMenu, this);
		this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);
		this.grid.getSelectionModel().un('selectionchange', this.onGridSelectionChange, this, {buffer: 10});
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.AbstractListUi.superclass.onDestroy.apply(this, arguments);
	},

	onGridContextMenu: function(grid, row, e) {
		e.preventDefault();
		var selModel = grid.getSelectionModel();
		if(!selModel.isSelected(row)) {
			selModel.selectRow(row);
		}
		this.getCtxMenu().showAt(e.getXY());
	},

	onGridSelectionChange: function(sm) {
		var numSelected = sm.getCount();
		this.actionEdit.setDisabled(numSelected !== 1);
		this.actionCopy.setDisabled(numSelected !== 1);
		this.actionDelete.setDisabled(numSelected === 0);
		this.actionExport.setDisabled(this.exportMethod === null);
	},

	onOpenEditWindow: function(action) {
		var itemUi = Ext.ComponentMgr.create({
			xtype: this.itemUiXType,
			domain: this.domain,
			record: this.getRecord(action),
			store: this.store,
			listUI: this,
			isNewRecord: action === 'copy' ? true : false
		});

		itemUi.show();
	},
	
	getRecord: function( action ) {
		if( action == 'add' ) {
			return null;
		} 
		else if( action == 'copy' )
		{
			var record = new this.store.recordType();
			var edit = this.grid.getSelectionModel().getSelected().copy();
			record.data = edit.data;
			record.data[ this.idProperty ] = null;
			
			return record;
		}
		return this.grid.getSelectionModel().getSelected();
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = _( 'Error' );
		var msg, code;
		
		if( response.error !== undefined ) {
			msg = response && response.error ? response.error.message : _( 'No error information available' );
			code = response && response.error ? response.error.code : 0;
		} else {
			msg = response && response.xhr.responseText[0].error ? response.xhr.responseText[0].error : _( 'No error information available' );
			code = response && response.xhr.responseText[0].tid ? response.xhr.responseText[0].tid : 0;
		}
		Ext.Msg.alert(title + ' (' + code + ')', msg);
	},

	setSiteParam: function(store) {
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options) {
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if (! this.domainProperty) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push({'==': condition});
	},

	setDomainProperty: function(store, action, records, options) {
		var rs = [].concat(records);

		Ext.each(rs, function(record) {
			if (! this.domainProperty) {
				this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
			}
			record.data[this.domainProperty] = this.domain;
		}, this);
	},

	typeColumnRenderer : function( typeId, metaData, record, rowIndex, colIndex, store, typeStore, displayField ) {
		var type = typeStore.getById(typeId);
		return type ? type.get(displayField) : typeId;
	},

	statusColumnRenderer : function(status, metaData) {
	    metaData.css = 'statusicon-' + Number( status );
	}
});
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

/**
 * Abtract ItemUi
 *
 * subclasses need to provide
 * - this.items
 * - this.mainForm reference
 *
 * @namespace   MShop
 * @class       MShop.panel.AbstractItemUi
 * @extends     Ext.Window
 */
MShop.panel.AbstractItemUi = Ext.extend(Ext.Window, {
	/**
	 * @cfg {Ext.data.Store} store (required)
	 */
	store: null,
	/**
	 * @cfg  {Ext.data.Record} record (optional)
	 */
	record: null,
	/**
	 * @cfg {MShop.panel.AbstractListUi}
	 */
	listUI: null,
	/**
	 * @cfg {Ext.from.FormPanel} mainForm
	 */
	mainForm: null,

	/**
	 * @type Boolean isSaveing
	 */
	isSaveing: false,

	maximized : true,
	layout: 'fit',
	modal: true,
	
	initComponent: function() {
		this.addEvents(
			/**
			 * @event beforesave
			 * Fired before record gets saved
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 */
			'beforesave',
			/**
			 * @event save
			 * Fired after record got saved
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 * @param {Function} ticketFn
			 */
			'save',
			/**
			 * @event validate
			 * Fired when validating user data. return false to signal invalid data
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 */
			'validate' );

		this.recordType = this.store.recordType;
		this.idProperty = this.idProperty || this.store.reader.meta.idProperty;

		this.initFbar();
		this.initRecord();

		this.store.on('beforewrite', this.onStoreBeforeWrite, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('write', this.onStoreWrite, this);

		MShop.panel.AbstractItemUi.superclass.initComponent.call(this);
	},

	setSiteCheck : function( itemUi )
	{
		itemUi.fieldsReadOnly = false;
		itemUi.readOnlyClass = '';

		if( itemUi.record && (itemUi.record.get( itemUi.siteidProperty ) != MShop.config.site['locale.site.id']) )
		{
			itemUi.fieldsReadOnly = true;
			itemUi.readOnlyClass = 'site-mismatch';
		}
	},

	initFbar: function() {
		this.fbar = {
			xtype: 'toolbar',
			buttonAlign: 'right',
			hideBorders: true,
			items: [
				{
					xtype: 'button',
					text: _('Cancel'),
					width: 120,
					scale: 'medium',
					handler: this.close,
					scope: this
				},
				{
					xtype: 'button',
					text: _('Save'),
					width: 120,
					scale: 'medium',
					handler: this.onSaveItem,
					scope: this
				}
			]
		};
	},

	initRecord: function() {
		if (! this.mainForm) {
			// wait till ref if here
			return this.initRecord.defer(50, this, arguments);
		}
		
		if (! this.record) {
			this.record = new this.recordType();
			this.isNewRecord = true;
		}

		this.mainForm.getForm().loadRecord(this.record);

		/** @todo Is this correct? */
		return true;
	},

	afterRender: function() {
		MShop.panel.AbstractItemUi.superclass.afterRender.apply(this, arguments);

		// kill x scrollers
		this.getEl().select('form').applyStyles({'overflow-x': 'hidden'});

		this.saveMask = new Ext.LoadMask(this.el, {msg: _('Saving')});
	},

	onDestroy: function() {
		this.store.un('beforewrite', this.onStoreBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);
		this.store.un('write', this.onStoreWrite, this);

		MShop.panel.AbstractItemUi.superclass.onDestroy.apply(this, arguments);
	},

	/**
	 * if it's not us who is saving, cancle save request
	 */
	onStoreBeforeWrite: function(store, action, rs, options ) {
		var records = Ext.isArray(rs) ? rs : [rs];

		if (records.indexOf(this.record) !== -1) {
			return this.isSaveing;
		}
	},


	onSaveItem: function() {
		// validate data
		if (! this.mainForm.getForm().isValid() && this.fireEvent('validate', this) !== false) {
			Ext.Msg.alert(_('Invalid Data'), _('Please recheck you data'));
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		// force record to be saved!
		this.record.dirty = true;

		if (this.fireEvent('beforesave', this, this.record) === false) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

		var recordRefIdProperty = this.listUI.listNamePrefix + "refid";
		var recordTypeIdProperty = this.listUI.listNamePrefix + "typeid";
		
		var index = this.store.findBy(function (item, index) {
			var recordRefId = this.record.get(recordRefIdProperty);
			var recordTypeId = this.mainForm.getForm().getFieldValues()[recordTypeIdProperty];
	
			var itemRefId = item.get(recordRefIdProperty);
			var itemTypeId = item.get(recordTypeIdProperty);
			
			var recordId = this.record.id;
			var itemId = index;
			
			if (! recordRefId || ! recordTypeId || ! itemRefId || ! itemTypeId)
				return false;
			
			return ( recordRefId == itemRefId && recordTypeId == itemTypeId && recordId != itemId );
		}, this);
		
		if (index != -1) {
			this.isSaveing = false;
			this.saveMask.hide();
			Ext.Msg.alert(_('Invalid Data'), _('This combination does already exist.'));
			return;
		}
		
		this.mainForm.getForm().updateRecord(this.record);
		
		if (this.isNewRecord) {
			this.store.add(this.record);
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if (! this.store.autoSave) {
			this.onAfterSave();
		}
	},

	onStoreException: function(proxy, type, action, options, response) {
		if (/*itwasus &&*/ this.isSaveing) {
			this.isSaveing = false;
			this.saveMask.hide();
		}
	},

	onStoreWrite: function(store, action, result, transaction, rs) {

		var records = Ext.isArray(rs) ? rs : [rs];

		if (records.indexOf(this.record) !== -1 && this.isSaveing) {
			var ticketFn = this.onAfterSave.deferByTickets(this),
				wrapTicket = ticketFn();
			
			this.fireEvent('save', this, this.record, ticketFn);
			wrapTicket();
		}
	},

	onAfterSave: function() {
		this.isSaveing = false;
		this.saveMask.hide();

		this.close();
	}
});

// NOTE: we need to register this abstract class so getByXtype can find decedents
Ext.reg('MShop.panel.abstractitemui', MShop.panel.AbstractItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.ListItemListUi = Ext.extend(MShop.panel.AbstractListUi, {
	/**
	 * @cfg {String} domain
	 */
	domain: null,

	/**
	 * @cfg {Function} getAdditionalColumns
	 */
	getAdditionalColumns: Ext.emptyFn,

	/**
	 * @property MShop.panel.AbstractItemUi itemUi
	 * parent itemUi this listpanel is child of
	 */
	itemUi: null,

	/**
	 * @property MShop.panel.AbstractListItemPickerUi listItemPickerUi
	 * parent listItemPickerUi this listpanel is agregated in
	 */
	listItemPickerUi: null,

	itemUiXType: 'MShop.panel.listitemitemui',

	initComponent: function() {
		// remove filter + paging
		this.gridConfig = this.gridConfig || {};
		this.gridConfig.tbar = null;
		this.gridConfig.bbar = null;

		this.autoExpandColumn = 'refcontent';

		// fetch ListItemPickerUi
		this.listItemPickerUi = this.findParentBy(function(c){
			return c.isXType(MShop.panel.AbstractListItemPickerUi, false);
		});

		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c){
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});
		this.itemUi.on('save', this.onItemUiSave, this);

		MShop.panel.ListItemListUi.superclass.initComponent.call(this);
		
		this.grid.getView().getRowClass = function(record, rowIndex, rowParams, store) { 
			if( ( status = record.get( this.listItemPickerUi.itemConfig.listNamePrefix + 'status' ) ) <= 0 ) {
				return  'statustext-' + Number( status );
			}
			return '';
		}.createDelegate(this);
	},

	initStore: function() {
		this.storeConfig = this.storeConfig || {};
		this.storeConfig.remoteSort = false;
		this.storeConfig.autoSave = false;

		MShop.panel.ListItemListUi.superclass.initStore.call(this);

		this.store.on('load', this.onStoreLoad, this);
		this.store.on('beforeload', this.setFilters, this);
		this.store.on('write', this.onStoreWrite, this);
		//this.store.on('exception', this.onStoreException, this);

	},

	onDestroy: function() {
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('write', this.onStoreWrite, this);
		//this.store.un('exception', this.onStoreException, this);

		MShop.panel.ListItemListUi.superclass.onDestroy.apply(this, arguments);
	},

	onOpenEditWindow: function(action) {
		if (action === 'add') {
			return Ext.Msg.alert(_('Select Item'), _('Please select an item on the right side and add it via drag and drop to this list.'));
		}

		return MShop.panel.ListItemListUi.superclass.onOpenEditWindow.apply(this, arguments);
	},

	onStoreLoad: function(store) {
		this.store.sort(this.listItemPickerUi.itemConfig.listNamePrefix + 'position', 'ASC');

		// create store of graph items ->
		//console.log(store.reader.jsonData);
	},

	onStoreWrite: function() {
		this.returnTicket();
	},

	onItemUiSave: function(itemUi, record, ticketFn) {
		// make sure all parentid are set
		this.store.each(function(r) {
			r.set(this.listItemPickerUi.itemConfig.listNamePrefix + 'parentid', record.id);
		}, this);

		if (this.store.save() !== -1) {
			this.returnTicket = ticketFn();
		}
	},

	setFilters: function(store, options) {
		if (this.itemUi.record.phantom) {
			// nothing to load
			return false;
		}

		// filter for refid
		//var parentIdProp = this.listItemPickerUi.listNamePrefix + ''
		var parentIdCriteria = {};
		parentIdCriteria[this.listItemPickerUi.itemConfig.listNamePrefix + 'parentid'] = this.itemUi.record.id;
		var domainCriteria = {};
		domainCriteria[this.listItemPickerUi.itemConfig.listNamePrefix + 'domain'] = this.domain;

		options.params = options.params || {};
		options.params.condition = {'&&': [
			{'==': parentIdCriteria},
			{'==': domainCriteria}
		]};

		return true;
	},

	getColumns : function() {
		var expr = this.listTypeCondition;
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: expr
			}
		};
		this.itemTypeStore = MShop.GlobalStoreMgr.get(this.listTypeControllerName, this.listTypeKey, storeConfig);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'id',
				header : _('Id'),
				width : 50,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'refid',
				header : _('Ref-Id'),
				width : 50,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'status',
				header : _('List Status'),
				width : 50,
				hidden : true,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'position',
				header : _('Position'),
				width : 50,
				hidden : true
			},
			{
				xtype : 'datecolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'datestart',
				header : _('Start date'),
				width : 120,
				format : 'Y-m-d H:i:s',
				hidden : true
			},
			{
				xtype : 'datecolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'dateend',
				header : _('End date'),
				width : 120,
				format : 'Y-m-d H:i:s',
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				hidden : true,
				renderer: function (value) {
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			},
			{
				xtype : 'datecolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'ctime',
				header : _('Created'),
				width : 120,
				format : 'Y-m-d H:i:s',
				hidden : true
			},
			{
				xtype : 'datecolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'mtime',
				header : _('Last modified'),
				width : 120,
				format : 'Y-m-d H:i:s',
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : this.listItemPickerUi.itemConfig.listNamePrefix + 'editor',
				header : _('Editor'),
				width : 50,
				hidden : true
			}
		].concat(this.getAdditionalColumns() || []);
	}
});



Ext.reg('MShop.panel.listitemlistui', MShop.panel.ListItemListUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 *
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.ListItemItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	layout : 'fit',
	modal : true,
	getAdditionalFields : Ext.emptyFn,

	initComponent : function() {

		this.title = _('List item details');

		this.items = [{
			xtype : 'form',
			border : false,
			flex : 1,
			layout: 'hbox',
			layoutConfig : {
				align : 'stretch'
			},
			ref : 'mainForm',
			autoScroll : true,
			items : [{
				xtype : 'fieldset',
				border : false,
				flex : 1,
				labelAlign : 'top',
				items : [{
					xtype : 'MShop.elements.status.combo',
					name : this.listUI.listNamePrefix + 'status'
				}, {
					xtype : 'combo',
					fieldLabel : _('List type'),
					name : this.listUI.listNamePrefix + 'typeid',
					mode : 'local',
					store : this.listUI.itemTypeStore,
					valueField : this.listUI.listTypeIdProperty,
					displayField : this.listUI.listTypeLabelProperty,
					forceSelection : false,
					triggerAction : 'all',
					allowBlank : false,
					typeAhead : true,
					anchor : '100%',
					emptyText : _('List type')
				}, {
					xtype : 'datefield',
					fieldLabel : _('Available from'),
					name : this.listUI.listNamePrefix + 'datestart',
					format : 'Y-m-d H:i:s',
					anchor : '100%',
					emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
				}, {
					xtype : 'datefield',
					fieldLabel : _('Available until'),
					name : this.listUI.listNamePrefix + 'dateend',
					format : 'Y-m-d H:i:s',
					anchor : '100%',
					emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
				}].concat( this.getAdditionalFields() || [] )
			}, {
					xtype: 'MShop.panel.listconfigui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get(this.listUI.listNamePrefix +'config') : {} )
			} ]
		}];
		
		MShop.panel.ListItemItemUi.superclass.initComponent.call(this);
	},
	
	onSaveItem: function() {
		// validate data
		if (! this.mainForm.getForm().isValid() && this.fireEvent('validate', this) !== false) {
			Ext.Msg.alert(_('Invalid Data'), _('Please recheck you data'));
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		// force record to be saved!
		this.record.dirty = true;

		this.getConfigRecords(this.store, this.record);
		
		if (this.fireEvent('beforesave', this, this.record) === false) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

		var recordRefIdProperty = this.listUI.listNamePrefix + "refid";
		var recordTypeIdProperty = this.listUI.listNamePrefix + "typeid";
		
		var index = this.store.findBy(function (item, index) {
			var recordRefId = this.record.get(recordRefIdProperty);
			var recordTypeId = this.mainForm.getForm().getFieldValues()[recordTypeIdProperty];
	
			var itemRefId = item.get(recordRefIdProperty);
			var itemTypeId = item.get(recordTypeIdProperty);
			
			var recordId = this.record.id;
			var itemId = index;
			
			if (! recordRefId || ! recordTypeId || ! itemRefId || ! itemTypeId)
				return false;
			
			return ( recordRefId == itemRefId && recordTypeId == itemTypeId && recordId != itemId );
		}, this);
		
		if (index != -1) {
			this.isSaveing = false;
			this.saveMask.hide();
			Ext.Msg.alert(_('Invalid Data'), _('This combination does already exist.'));
			return;
		}
		
		this.mainForm.getForm().updateRecord(this.record);
		
		if (this.isNewRecord) {
			this.store.add(this.record);
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if (! this.store.autoSave) {
			this.onAfterSave();
		}
	},
	
	getConfigRecords: function( store, record ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.listconfigui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}
		record.data[this.listUI.listNamePrefix + 'config'] = config;
	}
	
});

Ext.reg('MShop.panel.listitemitemui', MShop.panel.ListItemItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.AbstractListItemPickerUi = Ext.extend( Ext.Panel, {

	/**
	 * @cfg {Object} itemConfig (required)
	 */
	itemConfig : {},

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',

	layout : 'hbox',

	layoutConfig : {
		align : 'stretch'
	},

	initComponent : function() {
		this.items = [
			Ext.apply( {
				border : true,
				flex : 1,
				ref : 'itemListUi',
				gridConfig : {
					enableDragDrop : true,
					ddGroup : 'listItemsDDGroup'
				}
			}, this.itemConfig),
			Ext.apply( {
				border : true,
				ref : 'listUi',
				flex : 1,
				gridConfig : {
					enableDragDrop : true,
					ddGroup : 'listItemsDDGroup'
				}
			}, this.listConfig)
		];

		MShop.panel.AbstractListItemPickerUi.superclass.initComponent.call(this);

		Ext.apply(this.itemListUi.grid, {
			viewConfig: {
				emptyText: _('No Items'),
				getRowClass: function( record, index ) {
					
					siteid = MShop.config.site['locale.site.id'];
					if (record.phantom === false && record.get(this.itemListUi.siteidProperty) != siteid ) {
						return this.rowCssClass;
					}
					return '';

				}.createDelegate(this)
			}
		});
	},

	afterRender : function() {

		MShop.panel.AbstractListItemPickerUi.superclass.afterRender.apply(this, arguments);
		this.initDropZone();

		this.listUi.store.on('write', this.onListStoreWrite, this);
		this.listUi.store.on('beforeload', this.onListBeforeLoad, this);
		this.itemListUi.store.on('remove', this.onListStoreRemove, this);
	},


	initDropZone : function() {
		
		var availview = this.listUi.grid.getView();
		var assocview = this.itemListUi.grid.getView();

		if( !availview.scroller || !assocview.scroller ) {
			return this.initDropZone.defer( 10, this );
		}

		var assocDropTarget = new Ext.dd.DropTarget(
			assocview.scroller.dom,
			{
				ddGroup : 'listItemsDDGroup',
				notifyDrop : this.onListItemDrop.createDelegate( this )
			}
		);

		var availDropTarget = new Ext.dd.DropTarget(
			availview.scroller.dom,
			{
				ddGroup : 'listItemsDDGroup',
				notifyDrop : this.onAvailableListDrop.createDelegate( this )
			}
		);
		
		return null;
	},
	
	insertListItems: function(records, rowIndex) {
		var refStore = this.getRefStore();
		var clones = [];
		
		this.listTypeStore.filter([
			{
				property: this.itemConfig.listNamePrefix + 'type.domain',
				value   : this.itemListUi.domain
			}, {
				property: this.itemConfig.listNamePrefix + 'type.code',
				value   : 'default'
			}
		]);

		var typeId = this.listTypeStore.getCount() ? this.listTypeStore.getAt( 0 ).id : null;

		this.listTypeStore.clearFilter();

		Ext.each([].concat(records), function(record, id) {
			var refIdProperty = this.itemConfig.listNamePrefix + "refid";
			var typeIdPropery = this.itemConfig.listNamePrefix + "typeid";
			var recordTypeIdProperty = this.itemConfig.domain + ".typeid";
			
			if (refStore.getById(record.id)) {
				records.remove(record);

				// Get index of duplicated entry.
				var index = this.itemListUi.store.findBy(function (item) {
					return ( record.id == item.get(refIdProperty) && typeId == item.get(typeIdPropery) );
				}, this);

				if (index != -1) {
					// If entry is duplicated highlight it.
					Ext.fly(this.itemListUi.grid.getView().getRow(index)).highlight();
				} else {
					clones.push(record.copy());
				}
			} else {
				clones.push(record.copy());
			}
		}, this);

		records = clones;
		if (Ext.isEmpty(records)) {
			return;
		}

		// insert self in refStore
		refStore.add(records);

		// insert new list item records at the right position
		var rs = [], recordType = MShop.Schema.getRecord(this.itemListUi.recordName);


		Ext.each(
			records,
			function(record) {
				var data = {};
				data[this.itemConfig.listNamePrefix + 'refid'] = record.id;
				data[this.itemConfig.listNamePrefix + 'domain'] = this.itemListUi.domain;
				data[this.itemConfig.listNamePrefix + 'typeid'] = typeId;
				data[this.itemConfig.listNamePrefix + 'status'] = 1;
				rs.push(new recordType(data));
			},
			this );

		this.itemListUi.store.insert(rowIndex !== false ? rowIndex : this.itemListUi.store.getCount(), rs);

		this.itemListUi.store.each(function(record, idx) {
			record.set(this.itemConfig.listNamePrefix + 'position', idx);
		}, this);
	},

	
	onAvailableListDrop : function( ddSource, e, ddElement ) {

		if( !Ext.isArray( ddElement.selections ) ) {
			return;
		}
		
		var criteria = [];
		for( var i = 0; i < 1 /*ddElement.selections.length*/; i++ ) {
			criteria.push( {
				dataIndex : this.listUi.prefix + 'id',
				operator  : 'equals',
				value     : ddElement.selections[i].data[this.itemConfig.listNamePrefix + 'refid']
			} );
		}
		
		if( this.listUi.grid && this.listUi.grid.topToolbar && this.listUi.grid.topToolbar.filterGroup ) {
			this.listUi.grid.topToolbar.filterGroup.setFilterData( criteria/*, 'OR'*/ );
		}
	},
	

	onListItemDrop : function(ddSource, e, data) {

		var records = ddSource.dragData.selections,
			store = this.itemListUi.store,
			view = this.itemListUi.grid.getView(),
			t = e.getTarget(view.rowSelector),
			rowIndex = t ? view.findRowIndex(t) : store.getCount();

		if (ddSource.grid.store === store) {
			// reorder in same list
			var rs = [], posProperty = this.itemConfig.listNamePrefix + 'position';

			store.each(function(record, idx) {
				if (records.indexOf(record) < 0) {
					rs.push(record);
				}
			}, this);

			// records.reverse();
			Ext.each(records, function(record) {
				rs.splice(rowIndex, 0, record);
			});

			Ext.each(rs, function(record, idx) {
				record.set(posProperty, idx);
			});

			store.sort(posProperty, 'ASC');
		} else {
			// insert new records
			this.insertListItems(records, rowIndex);
		}

		return true;
	},

	onListStoreWrite: function(store, action, result, transaction, rs) {
		if (action === 'create') {
			// autoinsert in itemList
			this.insertListItems([].concat(rs), 0);
		}
	},

	onListBeforeLoad: function(store, options) {

		options.params = options.params || {};
		options.params.domain = this.listConfig.domain;
		options.params.parentid = null;

		var itemUi = this.findParentBy(function(c) {
			 return c.isXType(MShop.panel.AbstractItemUi, false);
		 });

		if ( !itemUi.record.isPhantom ) {
			options.params.parentid = itemUi.record.id;
		}
	},

	onListStoreRemove: function(store, record, index) {
		var refStore = this.getRefStore();
		refStore.removeAt(index);
	},

	getRefStore : function() {
		if (!this.refStore) {
			var recordName = this.listUi.recordName,
				idProperty = this.listUi.idProperty,
				data = { items : [], total : 0 };

			if( this.itemListUi.store.reader.jsonData &&
				this.itemListUi.store.reader.jsonData.graph &&
				this.itemListUi.store.reader.jsonData.graph[recordName] )
			{
				data = this.itemListUi.store.reader.jsonData.graph[recordName];
			}

			this.refStore = new Ext.data.JsonStore( {
				autoLoad : false,
				remoteSort : false,
				hasMultiSort : true,
				fields : MShop.Schema.getRecord(recordName),
				root : 'items',
				totalProperty : 'total',
				idProperty : idProperty,
				data : data
			});
		}

		return this.refStore;
	},

	onDestroy: function() {
		if (this.refStore) {
			this.refStore.destroy();
		}

		MShop.panel.AbstractListItemPickerUi.superclass.onDestroy.apply(this, arguments);
	},

	typeColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, typeStore, displayField) {
		if( Ext.isEmpty( displayField ) ) {
			throw new Ext.Error( 'Display field is empty' );
		}

		if( ! Ext.isEmpty( refId ) )
		{
			var refItem = typeStore.getById( refId );
			return ( refItem ? refItem.get( displayField ) : '' );
		}

		var value = '', typeId = record.get( this.itemConfig.listNamePrefix + 'typeid' );

		if( typeId ) {
			value = typeStore.getById( typeId ).get( displayField );
		}

		return value;
	},

	refColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, displayField) {

		var refItem = this.getRefStore().getById(refId);
		return (refItem ? refItem.get(displayField) : '');
	},

	refDateColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, displayField) {

		var refItem = this.getRefStore().getById(refId);
		renderer = Ext.util.Format.dateRenderer('Y-m-d H:i:s');

		return (refItem ? renderer( refItem.get(displayField) ) : '');
	},

	refDecimalColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, displayField) {

		var refItem = this.getRefStore().getById(refId),
			renderer = Ext.util.Format.numberRenderer(Ext.grid.NumberColumn.prototype.format);

		return (refItem ? renderer( refItem.get(displayField) ) : '');
	},

	refTypeColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, typeStore, idField, displayField) {

		var refItem = this.getRefStore().getById(refId);

		if (refItem && typeStore) {
			var type = typeStore.getById(refItem.get(idField));
			return type ? type.get(displayField) : '';
		}

		return '';
	},

	refLangColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, displayField) {

		var refItem = this.getRefStore().getById(refId);

		if (refItem) {
			return refItem.get(displayField) || _('all');
		}

		return '';
	},

	refStatusColumnRenderer : function(refId, metaData, record, rowIndex, colIndex, store, displayField) {

		var refItem = this.getRefStore().getById(refId);
		var value, status = 0;

		if( refItem && ( value = refItem.get( displayField ) ) ) {
		    status = Number( value );
		}

	    metaData.css = 'statusicon-' + status;
	}

});

Ext.reg('MShop.panel.abstractlistitempickerui', MShop.panel.AbstractListItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.AbstractTreeUi = Ext.extend(Ext.tree.TreePanel, {
	/**
	 * @cfg {String} recordName (required)
	 */
	recordName: null,

	/**
	 * @cfg {String} idProperty (required)
	 */
	idProperty: null,

	/**
	 * @cfg {String} idProperty (required)
	 */
	domain: null,

	/**
	 * @cfg {String} exportMethod (required)
	 */
	exportMethod: null,

	/**
	 * @cfg {Object} importMethod (optional)
	 */
	importMethod: null,

	rootVisible : true,
	useArrows : true,
	autoScroll : true,
	animate : true,
	enableDD : true,
	containerScroll : true,
	border : false,
	maskDisabled: true,

	initComponent : function()
	{
		// store is used for data transfer mainly
		this.initStore();

		this.on('movenode', this.onMoveNode, this);
		this.on('containercontextmenu', this.onContainerContextMenu, this);
		this.on('contextmenu', this.onContextMenu, this);
		this.on('dblclick', this.onOpenEditWindow.createDelegate(this, [ 'edit' ]), this);
		this.on('expandnode', this.onExpandNode, this);
		this.getSelectionModel().on('selectionchange', this.onSelectionChange, this, { buffer : 10 });

		MShop.panel.AbstractTreeUi.superclass.initComponent.call(this);
	},

	afterRender: function() {
		MShop.panel.AbstractTreeUi.superclass.afterRender.apply(this, arguments);

		this.disable();
		this.root.expand();
	},

	getCtxMenu : MShop.panel.AbstractListUi.prototype.getCtxMenu,


	onExport: function() {
		var win = new MShop.elements.exportlanguage.Window();
		win.on('save', this.finishExport, this);
		win.show();
	},

	finishExport: function(langwin, languages) {
		var selection = this.getSelectionModel().getSelectedNode();

		var downloader = new Ext.ux.file.Downloader({
			url: MShop.config.smd.target,
			params: {
				method: this.exportMethod,
				params: Ext.encode({
					items: selection.id,
					lang: languages,
					site: MShop.config.site['locale.site.code']
				})
			}
		}).start();
	},

	/**
	 * If there are no children do not display expand / collapse symbols
	 */
	onExpandNode : function (node) {
		var domain = this.domain;
		Ext.each(node["childNodes"], function(node) {
			if(node["attributes"].hasOwnProperty(domain + ".hasChildren") && node["attributes"][domain + ".hasChildren"] === false) {
				node.ui.ecNode.style.visibility = 'hidden';
			}
		}, this);
	},

	/**
	 * Init Loader
	 *
	 * @param {} showRootId
	 */
	initLoader : function(showRootId)
	{
		var domain = this.domain;
		this.loader = new Ext.tree.TreeLoader( {

				nodeParameter : 'items',
				paramOrder : [ 'site', 'items' ],
				baseParams : {
					site : MShop.config.site["locale.site.code"]
				},

				directFn : MShop.API[this.recordName].getTree,

				processResponse : function(response, node, callback, scope)
				{
					// reset root
					if (node.id === 'root') {
						// we create the node to have it in the store
						var newNode = this.createNode(response.responseText.items);

						node.setId(response.responseText.items[domain + '.id']);
						node.setText((showRootId !== true) ? response.responseText.items[domain + '.label'] : response.responseText.items[domain + '.id'] + " - " + response.responseText.items[domain + '.label']);
						node.getUI().addClass(newNode.attributes.cls);
						node.getOwnerTree().enable();
						node.getOwnerTree().actionAdd.setDisabled(node.id !== 'root');
					}
					// cut off item itself
					response.responseData = response.responseText.items.children;
					return Ext.tree.TreeLoader.prototype.processResponse.apply(this, arguments);
				},

				createNode : Ext.tree.TreeLoader.prototype.createNode.createInterceptor( this.inspectCreateNode, this )
		});

		this.loader.on('loadexception', function(loader, node, response) {

			if (node.id === 'root') {
				// no root node yet
				node.getUI().hide();
				node.getOwnerTree().enable();
				return;
			}
		}, this);
	},

	// NOTE: loading is done via treeloader, records get
	// created/inserted in this store from the treeloader also
	initStore : function()
	{
		this.store = new Ext.data.DirectStore(Ext.apply( {
				autoLoad : false,
				remoteSort : true,
				hasMultiSort : true,
				fields : MShop.Schema.getRecord(this.recordName),
				api : {
					create : MShop.API[this.recordName].insertItems,
					update : MShop.API[this.recordName].saveItems,
					destroy : MShop.API[this.recordName].deleteItems
				},
				writer : new Ext.data.JsonWriter( {
					writeAllFields : true,
					encode : false
				}),
				paramsAsHash : true,
				root : 'items',
				totalProperty : 'total',
				idProperty : this.idProperty
			},
			this.storeConfig ) );

		// make sure site param gets set for write actions
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
		this.store.on('write', this.onWrite, this);
	},

	onBeforeWrite : function(store, action, records, options)
	{
		if (action === 'create')
		{
			var parent = this.getSelectionModel().getSelectedNode();

			// NOTE: baseParams is the only hook we have here
			store.baseParams = store.baseParams || {};
			store.baseParams.parentid = parent ? parent.id : null;
		}

		MShop.panel.AbstractListUi.prototype.onBeforeWrite.apply(this, arguments);
	},

	onContainerContextMenu : function(tree, e)
	{
		e.preventDefault();

		// deselect all selections
		this.getSelectionModel().clearSelections();

		this.getCtxMenu().showAt(e.getXY());
	},

	onContextMenu : function(node, e)
	{
		e.preventDefault();

		// select ctx node
		this.getSelectionModel().select(node);

		this.getCtxMenu().showAt(e.getXY());
	},

	onDeleteSelectedItems : function()
	{
		var that = this;

		Ext.Msg.show({
			title: _('Delete items?'),
			msg: _('You are going to delete one or more items. Would you like to proceed?'),
			buttons: Ext.Msg.YESNO,
			fn: function (btn) {
				if (btn == 'yes') {
					var node = that.getSelectionModel().getSelectedNode(),
					root = that.getRootNode();

					if( node )
					{
						that.store.remove(that.store.getById(node.id));
						if (node === root) {
							that.getSelectionModel().clearSelections();

							that.setRootNode(new Ext.tree.AsyncTreeNode( { id : 'root' }));
							that.getRootNode().getUI().hide();
						} else {
							node.remove(true);
						}
					}
				}
			},
			animEl: 'elId',
			icon: Ext.MessageBox.QUESTION
		});
	},

	onSelectionChange : function(sm, node)
	{
		this.actionAdd.setDisabled(!node && this.getRootNode().id !== 'root');
		this.actionEdit.setDisabled(!node);
		this.actionDelete.setDisabled(!node);
		this.actionExport.setDisabled(!node);
	},

	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,

	onMoveNode : function(tree, node, oldParent, newParent, index)
	{
		var ref = node.nextSibling ? node.nextSibling.id : null;

		MShop.API[this.recordName].moveItems(
			MShop.config.site["locale.site.code"],
			node.id,
			oldParent.id,
			newParent.id,
			ref,
			function( success, response)
			{
				if (!success) {
					this.onStoreException(null, null, null, null, response);
				}
			},
			this );
	},

	onWrite : function(store, action, result, t, rs)
	{
		var selectedNode = this.getSelectionModel().getSelectedNode();

		Ext.each(
			[].concat(rs),
			function(r) {
				var newNode = this.getLoader().createNode(r.data);
				switch (action)
				{
					case 'create':
						if (selectedNode) {
							selectedNode.ui.ecNode.style.visibility = 'visible';
							selectedNode.appendChild(newNode);
						} else {
							this.setRootNode(newNode);
						}
						break;
					case 'update':
						// @TODO: rethink update vs.
						// recreate -> affects expands
						// of subnodes
						var oldNode = this.getNodeById(r.id);
						if (oldNode === this.getRootNode()) {
							this.setRootNode(newNode);
						} else {
							oldNode.parentNode.replaceChild(newNode, oldNode);
						}
						break;
					case 'destroy':
						break; // do nothing
					default:
						throw new Ext.Error('Invalid action "' + action + '"');
				}
			},
			this );
	},


	onOpenEditWindow : function(action)
	{
		var record;

		if( action !== 'add' ) {
			record = this.store.getById(this.getSelectionModel().getSelectedNode().id);
		}

		var itemUi = Ext.ComponentMgr.create(
			{
				xtype : 'MShop.panel.' + this.domain +'.itemui',
				record : record,
				store : this.store
			}
		);

		itemUi.show();
	},

	setDomainProperty : MShop.panel.AbstractListUi.prototype.setDomainProperty,
	setSiteParam : MShop.panel.AbstractListUi.prototype.setSiteParam
});/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.ListConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,
	autoExpandColumn : 'config-value',

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.ListConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					var selection = that.getSelectionModel().getSelections()[0];
					if (selection) {
						that.store.remove(selection);
						var data = {};
						Ext.each(that.store.data.items, function (item, index) {
							data[item.data.name] = item.data.value;
						}, this);
						that.data = data;
					}
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: { width: 250, sortable: true },
			columns: [
				{header: _('Name'), dataIndex: 'name', editor: { xtype: 'textfield'}},
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}, id:'config-value'}
			]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.listconfigui', MShop.panel.ListConfigUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Price',
	idProperty : 'price.id',
	siteidProperty : 'price.siteid',
	itemUiXType : 'MShop.panel.price.itemui',

	autoExpandColumn : 'price-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'price.currencyid',
			operator : 'startswith',
			value : ''
		} ]
	},

	getColumns : function() {
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Price_Type', this.domain);

		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'price.type.domain': this.domain } } ] }
			}
		};
		this.ItemTypeStore = MShop.GlobalStoreMgr.get('Price_Type', this.domain + '/price/type', storeConfig);

		return [ {
			xtype : 'gridcolumn',
			dataIndex : 'price.id',
			header : _('ID'),
			sortable : true,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.label',
			header: _('Label'),
			sortable: true,
			align: 'left',
			id : 'price-label'
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.status',
			header : _('Status'),
			sortable : true,
			width : 50,
			align: 'center',
			renderer : this.statusColumnRenderer.createDelegate(this)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.typeid',
			header : _('Type'),
			width : 70,
			renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "price.type.label" ], true)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.currencyid',
			header : _('Currency'),
			sortable : true,
			width : 50
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.quantity',
			header : _('Quantity'),
			align : 'right',
			sortable : true,
			width : 70,
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.value',
			header : _('Price'),
			sortable : true,
			width: 100,
			id : 'price-list-price',
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.rebate',
			header : _('Rebate'),
			sortable : true,
			width : 70,
			hidden : true,
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.costs',
			header : _('Costs'),
			sortable : true,
			width : 70,
			hidden : true,
			align : 'right'
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.taxrate',
			header : _('Tax rate'),
			sortable : true,
			width : 70,
			align : 'right',
			hidden : !MShop.Config.get( 'client/extjs/panel/price/listuismall/taxrate', 
			MShop.Config.get('client/extjs/panel/price/taxrate', false ) )
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.ctime',
			header : _('Created'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.mtime',
			header : _('Last modified'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.editor',
			header : _('Editor'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		} ];
	}
});

Ext.reg('MShop.panel.price.listuismall', MShop.panel.price.ListUiSmall);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'price.siteid',

	initComponent : function() {

		this.title = _('Price item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.price.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.price.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'price.id'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'price.label',
							allowBlank : true,
							emptyText : _('Label of the price')
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'price.status'
						}, {
							xtype : 'combo',
							fieldLabel : 'Type',
							name : 'price.typeid',
							mode : 'local',
							store : this.listUI.ItemTypeStore,
							displayField : 'price.type.label',
							valueField : 'price.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Default, special prices (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'price.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'MShop.elements.currency.combo',
							name : 'price.currencyid',
							emptyText : _('Currency (required)')
						}, {
							xtype : 'numberfield',
							name : 'price.quantity',
							fieldLabel : 'Minimum quantity',
							allowNegative : false,
							allowDecimals : false,
							allowBlank : false,
							value : 1
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Actual price',
							name : 'price.value',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Given rebate amount',
							name : 'price.rebate',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Costs per item',
							name : 'price.costs',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Tax rate in percent',
							name : 'price.taxrate',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'price.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'price.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'price.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.price.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['price.price'] : 'new';

		this.setTitle( 'Price: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.price.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.price.itemui', MShop.panel.price.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ItemPickerUi = Ext.extend( MShop.panel.AbstractListItemPickerUi, {

	title : _('Price'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Prices'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'price',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Prices'),
			xtype : 'MShop.panel.price.listuismall'
		});

		MShop.panel.price.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Price_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'price.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'price.typeid', 'price.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'reflabel',
				width : 100,
				hidden : true,
				renderer : this.refColumnRenderer.createDelegate(this, [ "price.label" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Currency'),
				id : 'refcurrency',
				width : 50,
				renderer : this.refColumnRenderer.createDelegate(this, [ "price.currencyid" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Quantity'),
				id : 'refquantiy',
				width : 70,
				align : 'right',
				renderer : this.refColumnRenderer.createDelegate(this, [ "price.quantity" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Price'),
				id : 'refcontent',
				align : 'right',
				renderer : this.refDecimalColumnRenderer.createDelegate(this, [ "price.value" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Rebate'),
				id : 'refrebate',
				width : 70,
				align : 'right',
				hidden : true,
				renderer : this.refDecimalColumnRenderer.createDelegate(this, [ "price.rebate" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Costs'),
				sortable : false,
				id : 'refshipping',
				width : 70,
				align : 'right',
				hidden : true,
				renderer : this.refDecimalColumnRenderer.createDelegate(this, [ "price.costs" ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Tax rate'),
				sortable : false,
				id : 'reftaxrate',
				width : 70,
				align : 'right',
				hidden : !MShop.Config.get( 'client/extjs/panel/price/itempickerui/taxrate', 
				MShop.Config.get('client/extjs/panel/price/taxrate', false ) ),
				renderer : this.refDecimalColumnRenderer.createDelegate(this, [ "price.taxrate" ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.price.itempickerui', MShop.panel.price.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements');

MShop.elements.ImportButton = Ext.extend(Ext.Button, {

	/**
	 * @cfg {Object} importMethod (required)
	 */
	importMethod: null,

	initComponent: function() {
		this.scope = this;
		this.handler = this.onFileSelect;

		this.plugins = this.plugins || [];
		this.browsePlugin = new Ext.ux.file.BrowsePlugin();
		this.plugins.push(this.browsePlugin);

		this.loadMask = new Ext.LoadMask(Ext.getBody(), {msg: _('Loading'), msgCls: 'x-mask-loading'});

		MShop.elements.ImportButton.superclass.initComponent.call(this);
	},

	/**
	 * @private
	 */
	onFileSelect: function(fileSelector) {
		this.loadMask.show();

		var uploader = new Ext.ux.file.Uploader({
			fileSelector: fileSelector,
			url: MShop.config.smd.target,
			methodName: this.importMethod,
			allowHTML5Uploads: false,
			HTML4params: {
				'params' : Ext.encode({
					site: MShop.config.site['locale.site.code']
				})
			}
		});

		uploader.on('uploadcomplete', this.onUploadSucess, this);
		uploader.on('uploadfailure', this.onUploadFail, this);

		uploader.upload(fileSelector.getFileList()[0]);
	},

	/**
	 * @private
	 */
	onUploadFail: function() {
		this.loadMask.hide();

		Ext.MessageBox.alert(
			_('Upload failed'),
			_('Could not upload file. Please notify your administrator.')).setIcon(Ext.MessageBox.ERROR);
	},

	/**
	 * @private
	 */
	onUploadSucess: function(uploader, record, response) {
		this.loadMask.hide();

		Ext.MessageBox.alert(
			_('Upload successful'),
			_('The texts of your uploaded file will be imported within a few minutes. You can check the status of the import in the "Job" panel of the "Overview" tab.') );
	}
});

Ext.reg('MShop.elements.importbutton', MShop.elements.ImportButton);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.elements.exportlanguage' );

/**
 * Window
 * 
 * @namespace MShop
 * @class MShop.elements.exportlanguage.Window
 * @extends Ext.Window
 */
MShop.elements.exportlanguage.Window = Ext.extend( Ext.Window, {

	modal: true,
	constrain: true,
	maximized: true,
	layout: 'fit',
	title: _( 'Select languages to export' ),
	
	gridItemList: null,
	gridList: null,
	refStore: null,

	recordName : 'Locale_Language',

	idProperty : 'locale.language.id',

	gridConfig : {
		enableDragDrop : true,
		ddGroup : 'listItemsDDGroup'
	},

	storeConfig : null,

	sortInfo : {
		field : 'locale.language.status',
		direction : 'DESC'
	},

	filterConfig : {
		filters : [ {
			dataIndex : 'locale.language.label',
			operator : 'contains',
			value : ''
		} ]
	},

	initComponent: function()
	{
		this.initFbar();
		this.initStore();
		this.initActions();

		if( this.filterConfig ) {
			this.filterConfig.filterModel = this.filterConfig.filterModel || MShop.Schema.getFilterModel( this.recordName );
		}

		this.gridItemList = new Ext.grid.GridPanel( Ext.apply( {
			border : true,
			ref : 'itemListUi',
			flex : 1,
			store: new Ext.data.ArrayStore( {
				fields: ['locale.language.status', 'locale.language.id', 'locale.language.label'],
				idIndex: 0
			} ),
			autoExpandColumn: 'dd-global-language-label',
			columns: [
				{
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.status',
					header : _( 'Status' ),
					width : 50,
					renderer: this.statusColumnRenderer
				}, {
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.id',
					header : _( 'Id' ),
					width : 50
				}, {
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.label',
					id: 'dd-global-language-label',
					header : _( 'Label' )
				}
			]
		}, this.gridConfig ) );

		this.gridList = new Ext.grid.GridPanel( Ext.apply( {
			border : true,
			ref : 'listUi',
			flex : 1,
			store: this.store,
			autoExpandColumn: 'global-language-label',
			columns: [
				{
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.status',
					header : _( 'Status' ),
					width : 50,
					sortable : true,
					renderer: this.statusColumnRenderer
				}, {
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.id',
					header : _( 'Id' ),
					sortable : true,
					width : 50
				}, {
					xtype : 'gridcolumn',
					dataIndex : 'locale.language.label',
					id: 'global-language-label',
					sortable : true,
					header : _( 'Label' )
				}
			],
			tbar: Ext.apply( {
				xtype: 'ux.advancedsearch',
				store: this.store
			}, this.filterConfig ),
			bbar: {
				xtype: 'MShop.elements.pagingtoolbar',
				store: this.store
			}
		}, this.gridConfig ) );

		this.items = [ {
			xtype: 'panel',
			border: false,
			layout: 'hbox',
			layoutConfig : {
				align : 'stretch'
			},
			items: [
				{
					xtype: 'panel',
					border: false,
					layout: 'fit',
					flex: 1,
					items: [ this.gridItemList ],
					tbar: [ this.actionDelete ]
				}, {
					xtype: 'panel',
					border: false,
					layout: 'fit',
					flex: 1,
					items: [ this.gridList ],
					tbar: [ this.actionDummy ]
				}
			]
		} ];

		this.gridItemList.on('rowcontextmenu', this.onGridItemListContextMenu, this);
		this.gridItemList.getSelectionModel().on( 'selectionchange', this.onGridSelectionChange, this, {buffer: 10} );

		MShop.elements.exportlanguage.Window.superclass.initComponent.call( this );
	},

	initActions: function()
	{
		this.actionDelete = new Ext.Action( {
			text: _( 'Delete' ),
			disabled: true,
			handler: this.onDeleteSelectedItems.createDelegate( this )
		} );

		this.actionDummy = new Ext.Action( {
			text: _( '' ),
			disabled: true
		} );
	},

	afterRender: function()
	{
		MShop.elements.exportlanguage.Window.superclass.afterRender.apply( this, arguments );
		this.initDropZone();
		this.gridItemList.store.on( 'remove', this.onListStoreRemove, this );
	},

	initDropZone : function()
	{
		var view = this.gridItemList.getView();

		if ( ! view.scroller ) {
			return this.initDropZone.defer( 10, this );
		}

		var gridDropTargetEl = this.gridItemList.getView().scroller.dom;
		var dropTarget = new Ext.dd.DropTarget(
			gridDropTargetEl, {
				ddGroup : 'listItemsDDGroup',
				notifyDrop : this.onListItemDrop.createDelegate( this )
			}
		);

		/** @todo Is this correct? */
		return true;
	},

	insertListItems: function( records, rowIndex )
	{
		// remove duplicates and highlight them in grid
		var refStore = this.getRefStore();
		var duplicats = [];
		var clones = [];

		Ext.each([].concat( records ), function( record ) {
			if( refStore.getById( record.id ) )
			{
				records.remove( record );

				var idx = this.gridItemList.store.find( 'locale.language.refid', record.id );
				Ext.fly( this.gridItemList.getView().getRow( idx ) ).highlight();
			} else {
				clones.push(record.copy());
			}
		}, this );

		records = clones;
		if( Ext.isEmpty( records ) ) {
			return;
		}

		refStore.add( records );

		var rs = [], recordType = MShop.Schema.getRecord( this.recordName );
		Ext.each(
			records,
			function( record ) {
				var data = {};
				data['locale.language.refid'] = record.id;
				data['locale.language.status'] = record.data['locale.language.status'];
				data['locale.language.id'] = record.data['locale.language.id'];
				data['locale.language.label'] = record.data['locale.language.label'];
				data['locale.language.domain'] = 'locale';
				data['locale.language.position'] = 0;
				rs.push( new recordType( data ) );
			},
			this );

		this.gridItemList.store.insert(rowIndex !== false ? rowIndex : this.gridItemList.store.getCount(), rs);

		this.gridItemList.store.each( function( record, idx ) {
			record.set( 'locale.language.position', idx );
		}, this );

	},

	onListItemDrop : function( ddSource, e, data )
	{
		var records = ddSource.dragData.selections,
			store = this.gridItemList.store,
			view = this.gridItemList.getView(),
			t = e.getTarget(view.rowSelector),
			rowIndex = t ? view.findRowIndex( t ) : store.getCount();

		if( ddSource.grid.store === store )
		{
			// reorder in same list
			var rs = [], posProperty = 'locale.language.position';

			store.each( function( record, idx ) {
				if( records.indexOf( record ) < 0 ) {
					rs.push( record );
				}
			}, this );

			Ext.each( records, function( record ) {
				rs.splice( rowIndex, 0, record );
			});

			Ext.each( rs, function( record, idx ) {
				record.set( posProperty, idx );
			});

			store.sort( posProperty, 'ASC' );
		} else {
			this.insertListItems( records, rowIndex );
		}

		return true;
	},

	getCtxMenu: function()
	{
		if( ! this.ctxMenu )
		{
			this.ctxMenu = new Ext.menu.Menu( {
				items: [ this.actionDelete ]
			} );
		}

		return this.ctxMenu;
	},

	onGridItemListContextMenu: function( grid, row, e )
	{
		e.preventDefault();

		var selModel = grid.getSelectionModel();
		if( ! selModel.isSelected( row ) ) {
			selModel.selectRow( row );
		}

		this.getCtxMenu().showAt(e.getXY());
	},

	onDeleteSelectedItems: function()
	{
		var that = this;

		Ext.Msg.show( {
			title: _( 'Delete items?' ),
			msg: _( 'You are going to delete one or more items. Would you like to proceed?' ),
			buttons: Ext.Msg.YESNO,
			fn: function( btn )
			{
				if( btn == 'yes' ) {
					that.gridItemList.store.remove( that.gridItemList.getSelectionModel().getSelections() );
				}
			},
			animEl: 'elId',
			icon: Ext.MessageBox.QUESTION
		} );
	},

	onListStoreRemove: function( store, record, index )
	{
		var refStore = this.getRefStore();
		refStore.removeAt( index );
	},

	onGridSelectionChange: function( sm )
	{
		var numSelected = sm.getCount();
		this.actionDelete.setDisabled( numSelected === 0 );
	},

	initFbar: function()
	{
		this.fbar = {
			xtype: 'toolbar',
			buttonAlign: 'right',
			hideBorders: true,
			items: [
				{
					xtype: 'button',
					text: _( 'Cancel' ),
					width: 120,
					scale: 'medium',
					handler: this.close,
					scope: this
				}, {
					xtype: 'button',
					text: _( 'Export' ),
					width: 120,
					scale: 'medium',
					handler: this.onExportItem,
					scope: this
				}
			]
		};
	},

	initStore: function()
	{
		this.store = new Ext.data.DirectStore( Ext.apply( {
			autoLoad: true,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord( this.recordName ),
			api: {
				read    : MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter( {
				writeAllFields: true,
				encode: false
			} ),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig ) );

		// make sure site param gets set for read/write actions
		this.store.on( 'beforeload', this.onBeforeLoad, this );
		this.store.on( 'exception', this.onStoreException, this );
		this.store.on( 'beforewrite', this.onBeforeWrite, this );
	},

	onBeforeLoad: function( store, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		options.params = options.params || {};
	},

	onBeforeWrite: function( store, action, records, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainProperty( store, action, records, options );
		}
	},

	onDestroy: function()
	{
		this.store.un( 'beforeload', this.onBeforeLoad, this );
		this.store.un( 'beforewrite', this.onBeforeWrite, this );
		this.store.un( 'exception', this.onStoreException, this );

		MShop.elements.exportlanguage.Window.superclass.onDestroy.apply( this, arguments );
	},

	onStoreException: function( proxy, type, action, options, response )
	{
		var title = _( 'Error' );
		var msg = response && response.error ? response.error.message : _( 'No error information available' );
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert( [title, ' (', code, ')'].join(''), msg );
	},

	setSiteParam: function( store )
	{
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function( store, options )
	{
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if( ! this.domainProperty ) {
			this.domainProperty = this.idProperty.replace( /\..*$/, '.domain' );
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push( {'==': condition} );
	},

	setDomainProperty: function( store, action, records, options )
	{
		var rs = [].concat( records );

		Ext.each( rs, function( record ) {
			if( ! this.domainProperty ) {
				this.domainProperty = this.idProperty.replace( /\..*$/, '.domain' );
			}
			record.data[this.domainProperty] = this.domain;
		}, this );
	},

	onExportItem: function()
	{
		var rs = [];

		this.gridItemList.store.each( function( record, idx ) {
			rs.push( record.data['locale.language.id'] );
		}, this);

		this.fireEvent( 'save', this, rs );
		this.close();
		
		Ext.MessageBox.alert(
			_('Export successful'),
			_('The file with the exported texts will be available within a few minutes. It can then be downloaded from the "Job" panel of the "Overview" tab.') );
	},

	statusColumnRenderer : function( status, metaData ) {
	    metaData.css = 'statusicon-' + Number( status );
	},

	getRefStore : function()
	{
		if ( ! this.refStore )
		{
			var recordName = this.recordName,
				idProperty = this.idProperty,
				data = { items : [], total : 0 };

			if( this.gridItemList.store.reader.jsonData &&
				this.gridItemList.store.reader.jsonData.graph &&
				this.gridItemList.store.reader.jsonData.graph[recordName] )
			{
				data = this.gridItemList.store.reader.jsonData.graph[recordName];
			}

			this.refStore = new Ext.data.JsonStore( {
				autoLoad : false,
				remoteSort : false,
				hasMultiSort : true,
				fields : MShop.Schema.getRecord( recordName ),
				root : 'items',
				totalProperty : 'total',
				idProperty : idProperty,
				data : data
			});
		}

		return this.refStore;
	}
});

Ext.reg( 'MShop.elements.language.window', MShop.elements.exportlanguage.Window );
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Text',
	idProperty : 'text.id',
	siteidProperty : 'text.siteid',
	itemUiXType : 'MShop.panel.text.itemui',

	autoExpandColumn : 'text-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'text.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	getColumns : function() {
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Text_Type', this.domain);
		
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'text.type.domain': this.domain } } ] }
			}
		};
		this.ItemTypeStore = MShop.GlobalStoreMgr.get('Text_Type', this.domain + '/text/type', storeConfig);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'text.id',
				header : _('ID'),
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.typeid',
				header : _('Type'),
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.typeStore, "text.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.languageid',
				header : _('Lang'),
				sortable : true,
				width : 50,
				renderer : MShop.elements.language.renderer
			}, {
				xtype : 'gridcolumn',
				id : 'text-list-label',
				dataIndex : 'text.label',
				header : _('Label'),
				sortable : true,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.content',
				header : _('Content'),
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.text.listuismall', MShop.panel.text.ListUiSmall);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'text.siteid',

	initComponent : function() {
	
		this.title = _('Text item details');
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.text.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.text.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					border : false,
					layout : 'fit',
					flex : 1,
					ref : '../../mainForm',
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						flex : 1,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'text.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'text.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'text.typeid',
							mode : 'local',
							store : this.listUI.ItemTypeStore,
							displayField : 'text.type.label',
							valueField : 'text.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Name, description, etc. (required)')
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'text.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'text.label'
						}, {
							xtype : MShop.Config.get('client/extjs/common/editor', 'htmleditor'),
							fieldLabel : 'Content',
							name : 'text.content',
							enableFont : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'text.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'text.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'text.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.text.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['text.text'] : 'new';
		this.setTitle( 'Text: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.text.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.text.itemui', MShop.panel.text.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ItemPickerUi = Ext.extend( MShop.panel.AbstractListItemPickerUi, {

	title : _('Text'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Texts'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'text',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Texts'),
			xtype : 'MShop.panel.text.listuismall'
		});

		MShop.panel.text.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {
		
		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Text_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'text.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'text.typeid', 'text.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 50,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'text.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'reflabel',
				hidden : true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'text.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Content'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'text.content' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.text.itempickerui', MShop.panel.text.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

// hook media picker into the text ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.text.ItemUi', 'MShop.panel.text.MediaItemPickerUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Text_List',
		idProperty : 'text.list.id',
		siteidProperty : 'text.list.siteid',
		listNamePrefix : 'text.list.',
		listTypeIdProperty : 'text.list.type.id',
		listTypeLabelProperty : 'text.list.type.label',
		listTypeControllerName : 'Text_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'text.list.type.domain': 'media' } } ] },
		listTypeKey : 'text/list/type/media'
	},
	listConfig : {
		domain : 'text',
		prefix : 'media.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

// hook media picker into the text ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.text.ItemUi', 'MShop.panel.text.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Text_List',
		idProperty : 'text.list.id',
		siteidProperty : 'text.list.siteid',
		listNamePrefix : 'text.list.',
		listTypeIdProperty : 'text.list.type.id',
		listTypeLabelProperty : 'text.list.type.label',
		listTypeControllerName : 'Text_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'text.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'text/list/type/attribute'
	},
	listConfig : {
		domain : [ 'text', 'product' ],
		prefix : 'attribute.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Media',
	idProperty : 'media.id',
	siteidProperty : 'media.siteid',
	itemUiXType : 'MShop.panel.media.itemui',

	autoExpandColumn : 'media-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'media.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	getColumns : function() {
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Media_Type', this.domain);
		
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'media.type.domain': this.domain } } ] }
			}
		};
		this.itemTypeStore = MShop.GlobalStoreMgr.get('Media_Type', this.domain + '/media/type', storeConfig);

		return [ {
			xtype : 'gridcolumn',
			dataIndex : 'media.id',
			header : _('ID'),
			sortable : true,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.status',
			header : _('Status'),
			sortable : true,
			width : 50,
			align: 'center',
			renderer : this.statusColumnRenderer.createDelegate(this)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.typeid',
			header : _('Type'),
			width : 70,
			renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "media.type.label" ], true)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.languageid',
			header : _('Lang'),
			sortable : true,
			width : 50,
			renderer : MShop.elements.language.renderer
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.mimetype',
			header : _('Mimetype'),
			sortable : true,
			width : 80,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.label',
			header : _('Label'),
			sortable : true,
			id : 'media-list-label'
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.preview',
			header : _('Preview'),
			renderer : this.previewRenderer.createDelegate(this),
			id : 'media-list-preview',
			width : 100
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.ctime',
			header : _('Created'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.mtime',
			header : _('Last modified'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'media.editor',
			header : _('Editor'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		} ];
	},

	previewRenderer : function(preview) {
		return "<img class='arcavias-admin-media-list-preview' src=\"" + preview + "\" />";
	}
});

Ext.reg('MShop.panel.media.listuismall', MShop.panel.media.ListUiSmall);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'media.siteid',

	initComponent : function() {
		
		this.title = _('Media item details');
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.media.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.media.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					border : false,
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						flex : 1,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'media.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'media.status'
						},
						{
							xtype : 'combo',
							fieldLabel : 'Type',
							name : 'media.typeid',
							mode : 'local',
							store : this.listUI.itemTypeStore,
							displayField : 'media.type.label',
							valueField : 'media.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('product picture, download, etc. (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'media.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'media.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Mimetype'),
							name : 'media.mimetype'
						}, {
							xtype : 'textfield',
							name : 'media.label',
							fieldLabel : 'Label',
							allowBlank : false,
							emptyText : _('Internal name (required)')
						}, {
							// NOTE: this is not used as a field, more like a
							// component which works on the whole record
							xtype : 'MShop.panel.media.mediafield',
							name : 'media.preview',
							width : 360,
							height : 280
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'media.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'media.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'media.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.media.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['media.label'] : 'new';
		this.setTitle( 'Media: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.media.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.media.itemui', MShop.panel.media.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ItemPickerUi = Ext.extend( MShop.panel.AbstractListItemPickerUi, {

	title : _('Media'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Media'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'media',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Media'),
			xtype : 'MShop.panel.media.listuismall'
		});

		MShop.panel.media.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Media_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'media.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'media.typeid', 'media.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Mimetype'),
				id : 'refmimetype',
				width : 80,
				hidden: true,
				sortable: true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'media.mimetype' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 50,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'media.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'media.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Preview'),
				id : 'refpreview',
				width : 100,
				renderer : this.refPreviewRenderer.createDelegate(this)
			} ];
	},

	refPreviewRenderer : function(refId, metaData, record, rowIndex, colIndex, store) {
		var refItem = this.getRefStore().getById(refId);
		return (refItem ? "<img class='mshop-admin-media-list-preview' src=\"" + refItem.get('media.preview') + "\" />" : '');
	}
});

Ext.reg('MShop.panel.media.itempickerui', MShop.panel.media.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

// hook text picker into the media ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.media.ItemUi', 'MShop.panel.media.TextItemPickerUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Media_List',
		idProperty : 'media.list.id',
		siteidProperty : 'media.list.siteid',
		listDomain : 'media',
		listNamePrefix : 'media.list.',
		listTypeIdProperty : 'media.list.type.id',
		listTypeLabelProperty : 'media.list.type.label',
		listTypeControllerName : 'Media_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'media.list.type.domain': 'text' } } ] },
		listTypeKey : 'media/list/type/text'
	},
	listConfig : {
		domain : 'media',
		prefix : 'text.'
	}
}, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

// hook media picker into the media ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.media.ItemUi', 'MShop.panel.media.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Media_List',
		idProperty : 'media.list.id',
		siteidProperty : 'media.list.siteid',
		listDomain : 'media',
		listNamePrefix : 'media.list.',
		listTypeIdProperty : 'media.list.type.id',
		listTypeLabelProperty : 'media.list.type.label',
		listTypeControllerName : 'Media_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'media.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'media/list/type/attribute'
	},
	listConfig : {
		domain : [ 'media', 'product' ],
		prefix : 'attribute.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.MediaField = Ext.extend(Ext.form.Field, {
    /**
     * @cfg {bool}
     */
    border: true,
    /**
     * @cfg {String}
     */
    defaultImage: '',
    
    cls: 'arcavias-admin-media-item-preview',
    
    defaultAutoCreate : {tag:'input', type:'hidden'},
    handleMouseEvents: true,
    
    initComponent: function() {
        this.scope = this;
        this.handler = this.onFileSelect;
        
        this.plugins = this.plugins || [];
        this.browsePlugin = new Ext.ux.file.BrowsePlugin({
            dropElSelector: 'div[class^=x-panel-body]'
        });
        this.plugins.push(this.browsePlugin);
        
        MShop.panel.media.MediaField.superclass.initComponent.call(this);
        
        this.imageSrc = this.defaultImage;
        if(this.border === true) {
            this.width = this.width;
            this.height = this.height;
        }
    },
    
    onRender: function(ct, position) {
        MShop.panel.media.MediaField.superclass.onRender.call(this, ct, position);
        
        // the container for the browe button
        this.buttonCt = Ext.DomHelper.insertFirst(ct, '<div>&nbsp;</div>', true);
        this.buttonCt.applyStyles({
            border: this.border === true ? '1px solid #B5B8C8' : '0'
        });
        this.buttonCt.setSize(this.width, this.height);
        
        this.loadMask = new Ext.LoadMask(this.buttonCt, {msg: _('Loading'), msgCls: 'x-mask-loading'});
        
        // the click to edit text container
        var clickToEditText = _('Click to edit');
        this.textCt = Ext.DomHelper.insertFirst(this.buttonCt, '<div class="x-ux-from-imagefield-text">' + clickToEditText + '</div>', true);
        this.textCt.setSize(this.width, this.height/3);
        var tm = Ext.util.TextMetrics.createInstance(this.textCt);
        tm.setFixedWidth(this.width);
        this.textCt.setStyle({top: ((this.height - tm.getHeight(clickToEditText)) / 2) + 'px'});
        
        // the image container
        // NOTE: this will atm. always be the default image for the first few miliseconds
        this.imageCt = Ext.DomHelper.insertFirst(this.buttonCt, '<img class="' + this.cls + '" src="' + MShop.config.baseurl.content + '/' + this.imageSrc + '"/>' , true);
        this.imageCt.setOpacity(0.2);
        this.imageCt.setStyle({
            position: 'absolute',
            top: '18px'
        });
        
        Ext.apply(this.browsePlugin, {
            buttonCt: this.buttonCt,
            renderTo: this.buttonCt
        });
    },
    
    afterRender: function() {
        MShop.panel.media.MediaField.superclass.afterRender.apply(this, arguments);
        
        this.itemUi = this.findParentBy(function(c){
            return c.isXType(MShop.panel.media.ItemUi, false);
        });
    },
    
    getValue: function() {
        var value = MShop.panel.media.MediaField.superclass.getValue.call(this);
        return value;
    },
    
    setValue: function(value) {
        MShop.panel.media.MediaField.superclass.setValue.call(this, value);
        
        if (! value || value == this.defaultImage) {
            this.imageSrc = this.defaultImage;
        } else {
            this.imageSrc = value;
        }
        this.updateImage();
    },
    
    /**
     * @private
     */
    onFileSelect: function(fileSelector) {
    	
        this.loadMask.show();
        
        var params = {
        	'site' : MShop.config.site["locale.site.code"],
        	'domain' : this.itemUi.domain
        };
        
        if( !this.itemUi.record.phantom ) {
	        params['media.id'] = this.itemUi.record.id;
        }
        
        var uploader = new Ext.ux.file.Uploader({
            fileSelector: fileSelector,
            url: MShop.config.smd.target,
            methodName: 'Media.uploadItem',
            allowHTML5Uploads: false,
            HTML4params: { 'params' : Ext.encode( params ) }
        });
        uploader.on('uploadcomplete', this.onUploadSucess, this);
        uploader.on('uploadfailure', this.onUploadFail, this);
        
        uploader.upload(fileSelector.getFileList()[0]);
    },
    
    /**
     * @private
     */
    onUploadFail: function() {
        this.loadMask.hide();
        Ext.MessageBox.alert(_('Upload Failed'), _('Could not upload image. Please notify your Administrator')).setIcon(Ext.MessageBox.ERROR);
    },
    
    onUploadSucess: function(uploader, record, response) {
        for (var field in response) {
            if (field.match(/\.status|\.label|\.typeid|\.langid/) && this.itemUi.record.get(field)) {
                continue;
            }
            
            // bullshit! -> sequence updateRecord fn?
            this.itemUi.record.data[field] = response[field];
            
            var formField = this.itemUi.mainForm.getForm().findField(field);
            if (formField && response[field]) {
                formField.setValue(response[field]);
            }
        }
        
        //this.setValue(response['media.preview']);
    },
    
    updateImage: function() {
        // only update when new image differs from current
        if(this.imageCt.dom.src.substr(-1 * this.imageSrc.length) != this.imageSrc) {
            var ct = this.imageCt.up('div');
            var img = Ext.DomHelper.insertAfter(this.imageCt, '<img class="' + this.cls + '" src="' + MShop.config.baseurl.content + this.imageSrc + '"/>' , true);
            // replace image after load
            img.on('load', function(){
                this.imageCt.remove();
                this.imageCt = img;
                this.textCt.setVisible(this.imageSrc == this.defaultImage);
                this.imageCt.setOpacity(this.imageSrc == this.defaultImage ? 0.2 : 1);
                this.loadMask.hide();
            }, this);
            img.on('error', function() {
                Ext.MessageBox.alert(_('Image Failed'), _('Could not load file. Please notify your Administrator')).setIcon(Ext.MessageBox.ERROR);
                this.loadMask.hide();
            }, this);
        }
    }
});

Ext.reg('MShop.panel.media.mediafield', MShop.panel.media.MediaField);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Attribute',
	idProperty : 'attribute.id',
	siteidProperty : 'attribute.siteid',
	itemUiXType : 'MShop.panel.attribute.itemui',
	exportMethod : 'Attribute_Export_Text.createJob',

	autoExpandColumn : 'attribute-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'attribute.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	initComponent : function()
	{
		this.title = _('Attribute');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.attribute.ListUi.superclass.initComponent.call(this);
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type');

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "attribute.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.code',
				header : _('Code'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.label',
				header : _('Label'),
				sortable : true,
				editable : false,
				id : 'attribute-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.position',
				header : _('Position'),
				sortable : true,
				width : 50,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'attribute.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'attribute.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			} ];
	}
} );

Ext.reg('MShop.panel.attribute.listui', MShop.panel.attribute.ListUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'attribute.siteid',

	initComponent : function() {

		this.title = _('Attribute item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );
		
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'attribute.type.domain': this.domain } } ] }
			}
		};
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type', this.domain + '/attribute/type', storeConfig);

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.attribute.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.attribute.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'attribute.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'attribute.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'attribute.typeid',
							mode : 'local',
							store : this.typeStore,
							displayField : 'attribute.type.label',
							valueField : 'attribute.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Attribute type, e.g width, size, etc. (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'attribute.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('Attribute code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'attribute.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal attribute name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Item position sharing the same type'),
							name : 'attribute.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'attribute.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'attribute.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'attribute.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.attribute.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['attribute.label'] : 'new';
		this.setTitle( 'Attribute: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.attribute.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.attribute.itemui', MShop.panel.attribute.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Attribute',
	idProperty : 'attribute.id',
	siteidProperty : 'attribute.siteid',
	itemUiXType : 'MShop.panel.attribute.itemui',
	exportMethod : 'Attribute_Export_Text.createJob',
	importMethod: 'Attribute_Import_Text.uploadFile',

	autoExpandColumn : 'attribute-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'attribute.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type');

		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'attribute.type.domain': this.domain } } ] }
			}
		};
		this.itemTypeStore = MShop.GlobalStoreMgr.get('Attribute_Type', this.domain + '/attribute/type', storeConfig);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'attribute.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.typeid',
				header : _('Type'),
				width : 80,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "attribute.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.code',
				header : _('Code'),
				sortable : true,
				width : 80
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.label',
				header : _('Label'),
				sortable : true,
				editable : false,
				id : 'attribute-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.position',
				header : _('Position'),
				sortable : true,
				width : 50,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.attribute.listuismall', MShop.panel.attribute.ListUiSmall);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Attribute'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Attributes'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'attribute',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Attributes'),
			xtype : 'MShop.panel.attribute.listuismall'
		});

		MShop.panel.attribute.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'attribute.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'attribute.typeid', 'attribute.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Code'),
				id : 'refcode',
				width : 80,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'attribute.code' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'attribute.label' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.attribute.itempickerui', MShop.panel.attribute.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

// hook text picker into the attribute ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.attribute.ItemUi', 'MShop.panel.attribute.TextItemPickerUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Attribute_List',
		idProperty : 'attribute.list.id',
		siteidProperty : 'attribute.list.siteid',
		listNamePrefix : 'attribute.list.',
		listTypeIdProperty : 'attribute.list.type.id',
		listTypeLabelProperty : 'attribute.list.type.label',
		listTypeControllerName : 'Attribute_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'attribute.list.type.domain': 'text' } } ] },
		listTypeKey : 'attribute/list/type/text'
	},
	listConfig : {
		domain : 'attribute',
		prefix : 'text.'
	}
}, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

// hook media picker into the attribute ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.attribute.ItemUi', 'MShop.panel.attribute.MediaItemPickerUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Attribute_List',
		idProperty : 'attribute.list.id',
		siteidProperty : 'attribute.list.siteid',
		listNamePrefix : 'attribute.list.',
		listTypeIdProperty : 'attribute.list.type.id',
		listTypeLabelProperty : 'attribute.list.type.label',
		listTypeControllerName : 'Attribute_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'attribute.list.type.domain': 'media' } } ] },
		listTypeKey : 'attribute/list/type/media'
	},
	listConfig : {
		domain : 'attribute',
		prefix : 'media.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.attribute');

// hook price picker into the attribute ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.attribute.ItemUi', 'MShop.panel.attribute.PriceItemPickerUi', {
	xtype : 'MShop.panel.price.itempickerui',
	itemConfig : {
		recordName : 'Attribute_List',
		idProperty : 'attribute.list.id',
		siteidProperty : 'attribute.list.siteid',
		listNamePrefix : 'attribute.list.',
		listTypeIdProperty : 'attribute.list.type.id',
		listTypeLabelProperty : 'attribute.list.type.label',
		listTypeControllerName : 'Attribute_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'attribute.list.type.domain': 'media' } } ] },
		listTypeKey : 'attribute/list/type/media'
	},
	listConfig : {
		domain : 'attribute',
		prefix : 'price.'
	}
}, 30);
/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ux.Portal = Ext.extend(Ext.Panel, {
    layout : 'column',
    autoScroll : true,
    cls : 'x-portal',
    defaultType : 'portalcolumn',
    
    initComponent : function(){
        Ext.ux.Portal.superclass.initComponent.call(this);
        this.addEvents({
            validatedrop:true,
            beforedragover:true,
            dragover:true,
            beforedrop:true,
            drop:true
        });
    },

    initEvents : function(){
        Ext.ux.Portal.superclass.initEvents.call(this);
        this.dd = new Ext.ux.Portal.DropZone(this, this.dropConfig);
    },
    
    beforeDestroy : function() {
        if(this.dd){
            this.dd.unreg();
        }
        Ext.ux.Portal.superclass.beforeDestroy.call(this);
    }
});

Ext.reg('portal', Ext.ux.Portal);

Ext.ux.Portal.DropZone = Ext.extend(Ext.dd.DropTarget, {
    
    constructor : function(portal, cfg){
        this.portal = portal;
        Ext.dd.ScrollManager.register(portal.body);
        Ext.ux.Portal.DropZone.superclass.constructor.call(this, portal.bwrap.dom, cfg);
        portal.body.ddScrollConfig = this.ddScrollConfig;
    },
    
    ddScrollConfig : {
        vthresh: 50,
        hthresh: -1,
        animate: true,
        increment: 200
    },

    createEvent : function(dd, e, data, col, c, pos){
        return {
            portal: this.portal,
            panel: data.panel,
            columnIndex: col,
            column: c,
            position: pos,
            data: data,
            source: dd,
            rawEvent: e,
            status: this.dropAllowed
        };
    },

    notifyOver : function(dd, e, data){
        var xy = e.getXY(), portal = this.portal, px = dd.proxy;

        // case column widths
        if(!this.grid){
            this.grid = this.getGrid();
        }

        // handle case scroll where scrollbars appear during drag
        var cw = portal.body.dom.clientWidth;
        if(!this.lastCW){
            this.lastCW = cw;
        }else if(this.lastCW != cw){
            this.lastCW = cw;
            portal.doLayout();
            this.grid = this.getGrid();
        }

        // determine column
        var col = 0, xs = this.grid.columnX, cmatch = false;
        for(var len = xs.length; col < len; col++){
            if(xy[0] < (xs[col].x + xs[col].w)){
                cmatch = true;
                break;
            }
        }
        // no match, fix last index
        if(!cmatch){
            col--;
        }

        // find insert position
        var p, match = false, pos = 0,
            c = portal.items.itemAt(col),
            items = c.items.items, overSelf = false;

        for(var length = items.length; pos < length; pos++){
            p = items[pos];
            var h = p.el.getHeight();
            if(h === 0){
                overSelf = true;
            }
            else if((p.el.getY()+(h/2)) > xy[1]){
                match = true;
                break;
            }
        }

        pos = (match && p ? pos : c.items.getCount()) + (overSelf ? -1 : 0);
        var overEvent = this.createEvent(dd, e, data, col, c, pos);

        if(portal.fireEvent('validatedrop', overEvent) !== false &&
           portal.fireEvent('beforedragover', overEvent) !== false){

            // make sure proxy width is fluid
            px.getProxy().setWidth('auto');

            if(p){
                px.moveProxy(p.el.dom.parentNode, match ? p.el.dom : null);
            }else{
                px.moveProxy(c.el.dom, null);
            }

            this.lastPos = {c: c, col: col, p: overSelf || (match && p) ? pos : false};
            this.scrollPos = portal.body.getScroll();

            portal.fireEvent('dragover', overEvent);

            return overEvent.status;
        }else{
            return overEvent.status;
        }

    },

    notifyOut : function(){
        delete this.grid;
    },

    notifyDrop : function(dd, e, data){
        delete this.grid;
        if(!this.lastPos){
            return;
        }
        var c = this.lastPos.c, 
            col = this.lastPos.col, 
            pos = this.lastPos.p,
            panel = dd.panel,
            dropEvent = this.createEvent(dd, e, data, col, c,
                pos !== false ? pos : c.items.getCount());

        if(this.portal.fireEvent('validatedrop', dropEvent) !== false &&
           this.portal.fireEvent('beforedrop', dropEvent) !== false){

            dd.proxy.getProxy().remove();
            panel.el.dom.parentNode.removeChild(dd.panel.el.dom);
            
            if(pos !== false){
                c.insert(pos, panel);
            }else{
                c.add(panel);
            }
            
            c.doLayout();

            this.portal.fireEvent('drop', dropEvent);

            // scroll position is lost on drop, fix it
            var st = this.scrollPos.top;
            if(st){
                var d = this.portal.body.dom;
                setTimeout(function(){
                    d.scrollTop = st;
                }, 10);
            }

        }
        delete this.lastPos;
    },

    // internal cache of body and column coords
    getGrid : function(){
        var box = this.portal.bwrap.getBox();
        box.columnX = [];
        this.portal.items.each(function(c){
             box.columnX.push({x: c.el.getX(), w: c.el.getWidth()});
        });
        return box;
    },

    // unregister the dropzone from ScrollManager
    unreg: function() {
        Ext.dd.ScrollManager.unregister(this.portal.body);
        Ext.ux.Portal.DropZone.superclass.unreg.call(this);
    }
});
/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ux.PortalColumn = Ext.extend(Ext.Container, {
    layout : 'anchor',
    //autoEl : 'div',//already defined by Ext.Component
    defaultType : 'portlet',
    cls : 'x-portal-column'
});

Ext.reg('portalcolumn', Ext.ux.PortalColumn);
/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ux.Portlet = Ext.extend(Ext.Panel, {
    anchor : '100%',
    frame : true,
    collapsible : true,
    draggable : true,
    cls : 'x-portlet'
});

Ext.reg('portlet', Ext.ux.Portlet);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.portal');

MShop.panel.portal.ItemUi = Ext.extend(Ext.Panel, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',

	initComponent : function() {
		this.title = _('Overview');
		this.items = [ {
			xtype:'portal',
			region:'center',
			items:[{
				columnWidth: 0.5,
				style:'margin:5px',
				items:[{
					xtype: 'MShop.panel.log.listuismall',
					layout: 'fit',
					height: 400,
					border: true,
					collapsible : true,
					draggable : true
				}]
			},
			{
				columnWidth: 0.5,
				style:'margin:5px',
				items:[{
					xtype: 'MShop.panel.job.listuismall',
					layout: 'fit',
					height: 400,
					border: true,
					collapsible : true,
					draggable : true
				}]
			}]
		}];

		MShop.panel.portal.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.portal.itemui', MShop.panel.portal.ItemUi);

//hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.portal.itemui', MShop.panel.portal.ItemUi, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Product',
	idProperty : 'product.id',
	siteidProperty : 'product.siteid',
	itemUiXType : 'MShop.panel.product.itemui',
	exportMethod : 'Product_Export_Text.createJob',
	importMethod: 'Product_Import_Text.uploadFile',

	autoExpandColumn : 'product-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Product');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.product.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		// make sure product type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.code',
				header : _('Code'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'product-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.suppliercode',
				header : _('Supplier'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.datestart',
				header : _('Start date'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.dateend',
				header : _('End date'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.product.listui', MShop.panel.product.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.product.listui', MShop.panel.product.ListUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'product.siteid',

	initComponent : function() {

		this.title = _('Product item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'product.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'product.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Product_Type'),
							displayField : 'product.type.label',
							valueField : 'product.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Product bundle, selection or article (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'product.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'product.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('EAN, SKU or article numer (required)')
						}, {
							xtype : 'textarea',
							fieldLabel : _('Label'),
							name : 'product.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal product name (required)')
						}, {
							xtype : 'combo',
							fieldLabel : _('Supplier'),
							name : 'product.suppliercode',
							store : MShop.GlobalStoreMgr.createStore('Supplier'),
							displayField : 'supplier.label',
							valueField : 'supplier.label',
							forceSelection : true,
							triggerAction : 'all',
							submitValue : true,
							typeAhead : true,
							emptyText : _('Product supplier (optional)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available from'),
							name : 'product.datestart',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available until'),
							name : 'product.dateend',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.editor'
						} ]
					} ]
				},{
					xtype: 'MShop.panel.product.stock.listuismall',
					layout: 'fit',
					flex: 1
				} ]
			} ]
		} ];

		MShop.panel.product.ItemUi.superclass.initComponent.call(this);
	},
	

	afterRender : function() {

		var label = this.record ? this.record.data['product.label'] : 'new';

		this.setTitle( 'Product: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	
	onStoreWrite : function(store, action, result, transaction, rs) {

        var records = Ext.isArray(rs) ? rs : [rs];
        var ids = [];

        MShop.panel.product.ItemUi.superclass.onStoreWrite.apply( this, arguments );
        
        for( var i = 0; i < records.length; i++ ) {
        	ids.push( records[i].id );
        }
         
        MShop.API.Product.finish( MShop.config.site["locale.site.code"], ids );
	}
});

Ext.reg('MShop.panel.product.itemui', MShop.panel.product.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Product',
	idProperty : 'product.id',
	siteidProperty : 'product.siteid',
	itemUiXType : 'MShop.panel.product.itemui',
	exportMethod : 'Product_Export_Text.createJob',

	autoExpandColumn : 'product-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.typeid',
				header : _('Type'),
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.code',
				header : _('Code'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.label',
				header : _('Label'),
				sortable : true,
				id : 'product-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.suppliercode',
				header : _('Supplier'),
				sortable : true,
				width : 100,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.datestart',
				header : _('Start date'),
				sortable : true,
				width : 120,
				hidden : true,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.dateend',
				header : _('End date'),
				sortable : true,
				width : 120,
				hidden : true,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.product.listuismall', MShop.panel.product.ListUiSmall);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Product'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Products'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'product',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Products'),
			xtype : 'MShop.panel.product.listuismall'
		});

		MShop.panel.product.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'product.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'product.typeid', 'product.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Code'),
				id : 'refcode',
				width : 100,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.code' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Supplier'),
				id : 'refsupplier',
				width : 120,
				hidden : true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.suppliercode' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Product start'),
				id : 'refprodstart',
				width : 120,
				hidden : true,
				renderer : this.refDateColumnRenderer.createDelegate(this, [ 'product.datestart' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Product end'),
				id : 'refprodend',
				width : 120,
				hidden : true,
				renderer : this.refDateColumnRenderer.createDelegate(this, [ 'product.dateend' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.product.itempickerui', MShop.panel.product.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook text picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.TextItemPickerUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'text' } } ] },
		listTypeKey : 'product/list/type/text'
	},
	listConfig : {
		domain : 'product',
		prefix : 'text.'
	}
}, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook media picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.MediaItemPickerUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'media' } } ] },
		listTypeKey : 'product/list/type/media'
	},
	listConfig : {
		domain : 'product',
		prefix : 'media.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook price picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.PriceItemPickerUi', {
	xtype : 'MShop.panel.price.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'price' } } ] },
		listTypeKey : 'product/list/type/price'
	},
	listConfig : {
		domain : 'product',
		prefix : 'price.'
	}
}, 30);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook product picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.ProductItemPickerUi', {
	xtype : 'MShop.panel.product.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'product' } } ] },
		listTypeKey : 'product/list/type/product'
	},
	listConfig : {
		prefix : 'product.'
	}
}, 40);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook media picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'product/list/type/attribute'
	},
	listConfig : {
		domain : 'product',
		prefix : 'attribute.'
	}
}, 50);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'product.tag.siteid',

	initComponent : function() {

		this.title = _('Product tag details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.tag.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.tag.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					border : false,
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						flex : 1,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.tag.id'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'product.tag.typeid',
							mode : 'local',
							store : this.listUI.typeStore,
							displayField : 'product.tag.type.label',
							valueField : 'product.tag.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Type of product tag (required)')
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'product.tag.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'product.tag.label',
							allowBlank : false,
							emptyText : _('Tag value (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.tag.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.tag.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.tag.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.product.tag.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['product.tag.label'] : 'new';

		this.setTitle( 'Product tag: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.tag.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.product.tag.itemui', MShop.panel.product.tag.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Product_Tag',
	idProperty : 'product.tag.id',
	siteidProperty : 'product.tag.siteid',
	itemUiXType : 'MShop.panel.product.tag.itemui',

	autoExpandColumn : 'product-tag-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.tag.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Tag_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.typeid',
				header : _('Type'),
				sortable : true,
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.tag.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.languageid',
				header : _('Lang'),
				sortable : true,
				width : 70,
				renderer : MShop.elements.language.renderer
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.label',
				header : _('Label'),
				sortable : true,
				id : 'product-tag-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.product.tag.listuismall', MShop.panel.product.tag.ListUiSmall);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Tags'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Tags'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'product/tag',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Tags'),
			xtype : 'MShop.panel.product.tag.listuismall'
		});

		MShop.panel.product.tag.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Tag_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'product.tag.typeid', 'product.tag.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 70,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'product.tag.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.tag.label' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.product.tag.itempickerui', MShop.panel.product.tag.ItemPickerUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

// hook product tag picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.ProductTagItemPickerUi', {
	xtype : 'MShop.panel.product.tag.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'product/tag' } } ] },
		listTypeKey : 'product/list/type/product/tag'
	},
	listConfig : {
		prefix : 'product.tag.'
	}
}, 100);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product.stock' );

MShop.panel.product.stock.ListUiSmall = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Product_Stock',
	idProperty : 'product.stock.id',
	siteidProperty : 'product.stock.siteid',
	itemUiXType : 'MShop.panel.product.stock.itemui',

	autoExpandColumn : 'product-stock-warehouse',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.stock.warehouse.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Stock');

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.product.stock.ListUiSmall.superclass.initComponent.call( this );
	},

	afterRender : function() {
		this.itemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		MShop.panel.product.stock.ListUiSmall.superclass.afterRender.apply( this, arguments );
	},

	onBeforeLoad: function( store, options ) {
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'product.stock.productid' : this.itemUi.record ? this.itemUi.record.id : null
				}
			} ]
		};

	},

	getColumns : function()
	{
		this.typeStore = MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse' );

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.id',
				header : _( 'Id' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.productid',
				header : _( 'Product Id' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouseid',
				header : _( 'Warehouse' ),
				align: 'center',
				id : 'product-stock-warehouse',
				renderer : this.typeColumnRenderer.createDelegate( this, [this.typeStore, "product.stock.warehouse.label" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.stocklevel',
				header : _( 'Quantity' ),
				sortable : true,
				align: 'center',
				width : 80
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.dateback',
				header : _( 'Dateback' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.ctime',
				header : _('Created'),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.mtime',
				header : _('Last modified'),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.product.stock.listuismall', MShop.panel.product.stock.ListUiSmall );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product.stock' );

MShop.panel.product.stock.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	recordName : 'Product_Stock',
	idProperty : 'product.stock.id',
	siteidProperty : 'product.stock.siteid',

	initComponent : function() {

		this.title = _( 'Stock & warehouse' );

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.stock.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.stock.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					id: 'MShop.panel.product.stock.ItemUi.BasicPanel.Title',
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'hidden',
							name : 'product.stock.productid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.stock.id'
						}, {
							xtype : 'combo',
							fieldLabel : 'Warehouse',
							name : 'product.stock.warehouseid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse', this.domain ),
							displayField : 'product.stock.warehouse.label',
							valueField : 'product.stock.warehouse.id',
							forceSelection : true,
							triggerAction : 'all',
							typeAhead : true,
							emptyText : _( 'Product repository (required)' ),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'product.stock.warehouse.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'numberfield',
							fieldLabel : 'Stock level',
							name : 'product.stock.stocklevel',
							emptyText : _( 'Quantity or empty if unlimited (optional)' )
						}, {
							xtype : 'datefield',
							fieldLabel : 'Back in stock',
							name : 'product.stock.dateback',
							format : 'Y-m-d H:i:s',
							emptyText : _( 'YYYY-MM-DD hh:mm:ss (optional)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.stock.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.stock.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.stock.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.product.stock.ItemUi.superclass.initComponent.call( this );
	},
	
	afterRender: function()
	{
		MShop.panel.product.stock.ItemUi.superclass.afterRender.apply( this, arguments );
		
		var oldTitle = Ext.getCmp('MShop.panel.product.stock.ItemUi.BasicPanel.Title').title;
		Ext.getCmp('MShop.panel.product.stock.ItemUi.BasicPanel.Title').setTitle( this.listUI.itemUi.record.data['product.label'] + ' - ' + oldTitle );
	},


	onSaveItem : function()
	{
		// validate data
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false ) {
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		// force record to be saved!
		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false ) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.mainForm.getForm().updateRecord( this.record );
		this.record.data['product.stock.productid'] = this.listUI.itemUi.record.id;

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	},


	onStoreException: function()
	{
		this.store.remove( this.record );
		MShop.panel.product.stock.ItemUi.superclass.onStoreException.apply( this, arguments );
	}
});

Ext.reg( 'MShop.panel.product.stock.itemui', MShop.panel.product.stock.ItemUi );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel' );

MShop.panel.AbstractUsedByListUi = Ext.extend( Ext.Panel, {

	/**
	 * @cfg {String} recordName (required)
	 */
	recordName: null,

	/**
	 * @cfg {String} idProperty (required)
	 */
	idProperty: null,

	/**
	 * @cfg {String} siteidProperty (required)
	 */
	siteidProperty: null,

	/**
	 * @cfg {String} itemUi xtype
	 */
	itemUiXType : null,

	/**
	 * @cfg {Object} sortInfo (optional)
	 */
	sortInfo: null,

	/**
	 * @cfg {String} autoExpandColumn (optional)
	 */
	autoExpandColumn: null,

	/**
	 * @cfg {Object} storeConfig (optional)
	 */
	storeConfig: null,

	/**
	 * @cfg {Object} gridConfig (optional)
	 */
	gridConfig: null,

	/**
	 * @cfg {String} parentIdProperty (required)
	 */
	parentIdProperty: null,

	/**
	 * @cfg {String} parentDomainPorperty (required)
	 */
	parentDomainPorperty: null,

	/**
	 * @cfg {String} parentRefIdProperty (required)
	 */
	parentRefIdProperty: null,

	layout: 'fit',

	initComponent : function()
	{
		this.initStore();

		this.listTypeStore = MShop.GlobalStoreMgr.get( this.recordName + '_Type', 'Product' );
		this.productTypeStore = MShop.GlobalStoreMgr.get( 'Product_Type', 'Product' );

		MShop.panel.AbstractUsedByListUi.superclass.initComponent.call( this );
	},

	initStore: function()
	{
		this.store = new Ext.data.DirectStore( Ext.apply( {
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord( this.recordName ),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter( {
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig ) );

		this.store.on( 'beforeload', this.onBeforeLoad, this );
		this.store.on( 'exception', this.onStoreException, this );
		this.store.on( 'beforewrite', this.onBeforeWrite, this );
	},

	afterRender: function()
	{
		MShop.panel.AbstractUsedByListUi.superclass.afterRender.apply( this, arguments );

		this.ParentItemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		if ( !this.store.autoLoad ) {
			this.store.load();
		}

		this.grid = new Ext.grid.GridPanel( Ext.apply( {
			border: false,
			store: this.store,
			autoExpandColumn: this.autoExpandColumn,
			columns: this.getColumns()
		}, this.gridConfig ) );

		this.grid.on( 'rowdblclick', this.onOpenEditWindow.createDelegate( this, ['edit']), this );
		this.add( this.grid );
	},

	onBeforeLoad: function( store, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		var domainFilter = {};
		domainFilter[this.parentDomainPorperty] = 'product';

		var refIdFilter = {};
		
		refIdFilter[this.parentRefIdProperty] = null;
		if( this.ParentItemUi.record.data['product.id'] ) {
			refIdFilter[this.parentRefIdProperty] = this.ParentItemUi.record.data['product.id'];
		}
		
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				 	'==' : domainFilter
				}, {
					'==' : refIdFilter
			} ]
		};
		
		options.params.start = 0;
		options.params.limit = 0x7fffffff;
	},

	onBeforeWrite: function( store, action, records, options )
	{
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainProperty( store, action, records, options );
		}
	},

	onDestroy: function()
	{
		this.store.un( 'beforeload', this.onBeforeLoad, this );
		this.store.un( 'beforewrite', this.onBeforeWrite, this );
		this.store.un( 'exception', this.onStoreException, this );

		MShop.panel.AbstractUsedByListUi.superclass.onDestroy.apply( this, arguments );
	},

	onStoreException: function( proxy, type, action, options, response )
	{
		var title = _( 'Error' );
		var msg = response && response.error ? response.error.message : _( 'No error information available' );
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function( store )
	{
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options)
	{
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if( !this.domainProperty ) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push( {'==': condition} );
	},

	setDomainProperty: function( store, action, records, options )
	{
		var rs = [].concat( records );

		Ext.each(rs, function( record ) {
			if( !this.domainProperty ) {
				this.domainProperty = this.idProperty.replace( /\..*$/, '.domain' );
			}
			record.data[this.domainProperty] = this.domain;
		}, this );
	},

	onOpenEditWindow: function( action ) {
		var record = this.grid.getSelectionModel().getSelected();
		var parentRecord = this.ParentItemUi.store.getById( record.data[this.parentIdProperty] );

		var itemUi = Ext.ComponentMgr.create( {
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : parentRecord,
			store: this.ParentItemUi.store,
			listUI: this
		} );

		itemUi.show();
	},

	listTypeColumnRenderer : function( listTypeId, metaData, record, rowIndex, colIndex, store, listTypeStore, displayField ) {

		var list = listTypeStore.getById( listTypeId );

		return list ? list.get( displayField ) : listTypeId;
	},

	statusColumnRenderer : function( listTypeId, metaData, record, rowIndex, colIndex, store, listTypeStore, displayField ) {

		var list = listTypeStore.getById( listTypeId );

	    metaData.css = 'statusicon-' + ( list ? Number( list.get( displayField ) ) : 0 );
	},

	productTypeColumnRenderer : function( typeId, metaData, record, rowIndex, colIndex, store, typeStore, productTypeStore, prodctId, displayField ) {

		var type = typeStore.getById( typeId );
		var productType = productTypeStore.getById( type.data[prodctId] );

		return productType ? productType.get( displayField ) : typeId;
	}
});
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product' );

MShop.panel.product.UsedByCatalogListUi = Ext.extend( MShop.panel.AbstractUsedByListUi, {

	recordName : 'Catalog_List',
	idProperty : 'catalog.list.id',
	siteidProperty : 'catalog.list.siteid',
	itemUiXType : 'MShop.panel.catalog.itemui',

	autoExpandColumn : 'catalog-list-autoexpand-column',

	sortInfo : {
		field : 'catalog.list.type.code',
		direction : 'ASC'
	},

	parentIdProperty : 'catalog.list.parentid',
	parentDomainPorperty : 'catalog.list.domain',
	parentRefIdProperty : 'catalog.list.refid',

	initComponent : function()
	{
		MShop.panel.product.UsedByCatalogListUi.superclass.initComponent.call( this );

		this.title = _( 'Category' );

		this.catalogStore = MShop.GlobalStoreMgr.get( 'Catalog' );
	},

	onOpenEditWindow: function( action ) {
		var record = this.grid.getSelectionModel().getSelected();
		var parentRecord = this.catalogStore.getById( record.data[this.parentIdProperty] );

		parentRecord.data['status'] = parentRecord.data['catalog.status'];
		parentRecord.data['label'] = parentRecord.data['catalog.label'];
		parentRecord.data['code'] = parentRecord.data['catalog.code'];

		var itemUi = Ext.ComponentMgr.create( {
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : parentRecord,
			store: this.catalogStore,
			listUI: this
		} );

		itemUi.show();
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.id',
				header : _( 'List ID' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.typeid',
				header : _( 'List type' ),
				sortable : true,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.listTypeStore, "catalog.list.type.label" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.datestart',
				header : _( 'List start date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.dateend',
				header : _( 'List end date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.position',
				header : _( 'List position' ),
				sortable : true,
				width : 70,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.mtime',
				header : _( 'Modification time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.ctime',
				header : _( 'Creation time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.editor',
				header : _( 'Editor' ),
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category ID' ),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Status' ),
				sortable : false,
				width : 50,
				renderer : this.statusColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.status" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category code' ),
				sortable : false,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.code" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category label' ),
				sortable : false,
				width : 100,
				id : 'catalog-list-autoexpand-column',
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.label" ], true)
			}
		];
	}
});

Ext.reg( 'MShop.panel.product.usedbycataloglistui', MShop.panel.product.UsedByCatalogListUi );

//hook parent product list into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.UsedByCatalogListUi', MShop.panel.product.UsedByCatalogListUi, 100);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product' );

MShop.panel.product.UsedByProductListUi = Ext.extend( MShop.panel.AbstractUsedByListUi, {

	recordName : 'Product_List',
	idProperty : 'product.list.id',
	siteidProperty : 'product.list.siteid',
	itemUiXType : 'MShop.panel.product.itemui',

	autoExpandColumn : 'product-list-autoexpand-column',

	sortInfo : {
		field : 'product.list.parentid',
		direction : 'ASC'
	},

	parentIdProperty : 'product.list.parentid',
	parentDomainPorperty : 'product.list.domain',
	parentRefIdProperty : 'product.list.refid',

	initComponent : function()
	{
		MShop.panel.product.UsedByProductListUi.superclass.initComponent.call( this );

		this.title = _( 'Used by' );
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.list.id',
				header : _( 'List ID' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.typeid',
				header : _( 'List type' ),
				sortable : true,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.listTypeStore, "product.list.type.label" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.datestart',
				header : _( 'List start date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.dateend',
				header : _( 'List end date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.position',
				header : _( 'List position' ),
				sortable : true,
				width : 70,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.mtime',
				header : _( 'List modification time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.ctime',
				header : _( 'List creation time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.editor',
				header : _( 'List editor' ),
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product ID' ),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Status' ),
				sortable : false,
				width : 50,
				renderer : this.statusColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.status" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product type' ),
				sortable : false,
				width : 100,
				renderer : this.productTypeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, this.productTypeStore, "product.typeid", "product.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product code' ),
				sortable : false,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.code" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product label' ),
				sortable : false,
				id : 'product-list-autoexpand-column',
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product Supplier' ),
				sortable : false,
				width : 100,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.suppliercode" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product start date' ),
				format : 'Y-m-d H:i:s',
				sortable : false,
				width : 120,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.datestart" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product end date' ),
				format : 'Y-m-d H:i:s',
				sortable : false,
				width : 120,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.dateend" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product modification time' ),
				format : 'Y-m-d H:i:s',
				sortable : false,
				width : 120,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.mtime" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product creation time' ),
				format : 'Y-m-d H:i:s',
				sortable : false,
				width : 120,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.ctime" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.list.parentid',
				header : _( 'Product editor' ),
				sortable : false,
				width : 100,
				hidden : true,
				renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.editor" ], true)
			}
		];
	}
});

Ext.reg( 'MShop.panel.product.usedbyproductlistui', MShop.panel.product.UsedByProductListUi );

//hook parent product list into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.UsedByProductListUi', MShop.panel.product.UsedByProductListUi, 110);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.TreeUi = Ext.extend(MShop.panel.AbstractTreeUi, {

	rootVisible : true,
	useArrows : true,
	autoScroll : true,
	animate : true,
	enableDD : true,
	containerScroll : true,
	border : false,
	ddGroup : 'MShop.panel.catalog',
	maskDisabled: true,
	domain: 'catalog',

	recordName : 'Catalog',
	idProperty : 'catalog.id',
	exportMethod : 'Catalog_Export_Text.createJob',
	importMethod: 'Catalog_Import_Text.uploadFile',


	initComponent : function()
	{
		this.title = _('Catalog');
		this.domain = 'catalog';
		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		this.recordClass = MShop.Schema.getRecord(this.recordName);

		this.initLoader(true);

		// fake a root -> needed by extjs
		this.root = new Ext.tree.AsyncTreeNode( { id : 'root' } );

		MShop.panel.catalog.TreeUi.superclass.initComponent.call(this);
	},

	inspectCreateNode : function(attr)
	{
		// adding label to object as text is necessary
		var status = attr['catalog.status'];

		attr.id = attr['catalog.id'];
		attr.text = attr['catalog.id'] + " - " + attr['catalog.label'];
		attr.code = attr['catalog.code'];
		attr.cls = 'statustext-' + status;

		// create record and insert into own store
		this.store.suspendEvents(false);
		var oldRecord = this.store.getById(attr['catalog.id']);
		this.store.remove(oldRecord);

		this.store.add( [ new this.recordClass( {
				id : attr.id,
				status : status,
				code : attr['catalog.code'],
				label : attr['catalog.label'],
				'catalog.hasChildren' : attr['catalog.hasChildren'],
				'catalog.config' : attr['catalog.config'],
				'catalog.siteid' :attr['catalog.siteid'],
				'catalog.ctime' : attr['catalog.ctime'],
				'catalog.mtime' : attr['catalog.mtime'],
				'catalog.editor' : attr['catalog.editor']
		}, attr.id ) ] );

		this.store.resumeEvents();
	}
});



// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.catalog.treeui', MShop.panel.catalog.TreeUi, 30);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',
	siteidProperty : 'catalog.siteid',

	initComponent : function() {
		this.title = _( 'Catalog item details' );

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.catalog.ItemUi',
			plugins : ['ux.itemregistry'],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.catalog.ItemUi.BasicPanel',
				plugins : ['ux.itemregistry'],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('Category code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'label',
							allowBlank : false,
							emptyText : _( 'Category name (required)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'catalog.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'catalog.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'catalog.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.catalog.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('catalog.config') : {} )
				} ]
			} ]
		} ];
		
		this.store.on('beforesave', this.onBeforeSave, this);
		
		MShop.panel.catalog.ItemUi.superclass.initComponent.call( this );
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['label'] : 'new';
		this.setTitle( 'Catalog: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.catalog.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	
	onBeforeSave: function( store, data ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.catalog.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['catalog.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['catalog.config'] = config;
		}
	},

	
	onSaveItem: function() {
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false )
		{
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false )
		{
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.record.beginEdit();
		this.record.set( 'catalog.label', this.mainForm.getForm().findField( 'label' ).getValue() );
		this.record.set( 'catalog.status', this.mainForm.getForm().findField( 'status' ).getValue() );
		this.record.set( 'catalog.code', this.mainForm.getForm().findField( 'code' ).getValue() );
		this.record.endEdit();

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	}
});

Ext.reg( 'MShop.panel.catalog.itemui', MShop.panel.catalog.ItemUi );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,
	autoExpandColumn : 'catalog-config-value',

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.catalog.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					var selection = that.getSelectionModel().getSelections()[0];
					if (selection) {
						that.store.remove(selection);
						var data = {};
						Ext.each(that.store.data.items, function (item, index) {
							data[item.data.name] = item.data.value;
						}, this);
						that.data = data;
					}
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: { width: 250, sortable: true },
			columns: [
				{header: _('Name'), dataIndex: 'name', editor: { xtype: 'textfield'}},
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}, id:'catalog-config-value'}
			]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.catalog.configui', MShop.panel.catalog.ConfigUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

// hook text picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.TextItemPickerUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Catalog_List',
		idProperty : 'catalog.list.id',
		siteidProperty : 'catalog.list.siteid',
		listNamePrefix : 'catalog.list.',
		listTypeIdProperty : 'catalog.list.type.id',
		listTypeLabelProperty : 'catalog.list.type.label',
		listTypeControllerName : 'Catalog_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'catalog.list.type.domain': 'text' } } ] },
		listTypeKey : 'catalog/list/type/text'
	},
	listConfig : {
		domain : 'catalog',
		prefix : 'text.'
	}
}, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

// hook media picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.MediaItemPickerUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Catalog_List',
		idProperty : 'catalog.list.id',
		siteidProperty : 'catalog.list.siteid',
		listNamePrefix : 'catalog.list.',
		listTypeIdProperty : 'catalog.list.type.id',
		listTypeLabelProperty : 'catalog.list.type.label',
		listTypeControllerName : 'Catalog_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'catalog.list.type.domain': 'media' } } ] },
		listTypeKey : 'catalog/list/type/media'
	},
	listConfig : {
		domain : 'catalog',
		prefix : 'media.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

// hook product picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.ProductItemPickerUi', {
	xtype : 'MShop.panel.product.itempickerui',
	itemConfig : {
		recordName : 'Catalog_List',
		idProperty : 'catalog.list.id',
		siteidProperty : 'catalog.list.siteid',
		listNamePrefix : 'catalog.list.',
		listTypeIdProperty : 'catalog.list.type.id',
		listTypeLabelProperty : 'catalog.list.type.label',
		listTypeControllerName : 'Catalog_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'catalog.list.type.domain': 'product' } } ] },
		listTypeKey : 'catalog/list/type/product'
	},
	listConfig : {
		prefix : 'product.'
	}
}, 30);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.catalog');

// hook attribute picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Catalog_List',
		idProperty : 'catalog.list.id',
		siteidProperty : 'catalog.list.siteid',
		listNamePrefix : 'catalog.list.',
		listTypeIdProperty : 'catalog.list.type.id',
		listTypeLabelProperty : 'catalog.list.type.label',
		listTypeControllerName : 'Catalog_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'catalog.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'catalog/list/type/attribute'
	},
	listConfig : {
		domain : 'catalog',
		prefix : 'attribute.'
	}
}, 50);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Service',
	idProperty : 'service.id',
	siteidProperty : 'service.siteid',
	itemUiXType : 'MShop.panel.service.itemui',

	autoExpandColumn : 'service-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'service.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Service');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.service.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		// make sure service type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Service_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "service.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.code',
				header : _('Code'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.provider',
				header : _('Provider'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'service-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.position',
				header : _('Position'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				renderer: function (value) {	
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'service.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'service.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}

} );

Ext.reg('MShop.panel.service.listui', MShop.panel.service.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.service.listui', MShop.panel.service.ListUi, 50);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,


	initComponent : function() {
		this.title = _('Service item details');
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.service.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.service.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'service.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'service.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'service.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Service_Type'),
							displayField : 'service.type.label',
							valueField : 'service.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Payment or delivery (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'service.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('Unique service code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Provider'),
							name : 'service.provider',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Name of the service provider class (required)')
						}, {
							xtype : 'textarea',
							fieldLabel : _('Label'),
							name : 'service.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal service name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Position'),
							name : 'service.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'service.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'service.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'service.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.service.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('service.config') : {} )
				} ]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);

		MShop.panel.service.ItemUi.superclass.initComponent.call(this);
	},
	

	afterRender : function()
	{
		var label = this.record ? this.record.data['service.label'] : 'new';

		this.setTitle( 'Service: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},


	onBeforeSave: function( store, data ) {

		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.service.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['service.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['service.config'] = config;
		}
	}
});

Ext.reg('MShop.panel.service.itemui', MShop.panel.service.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

//hook text picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.TextItemPickerUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'text' } } ] },
		listTypeKey : 'service/list/type/text'
	},
	listConfig : {
		domain : 'service',
		prefix : 'text.'
	}
}, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

// hook media picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.AttributeItemPickerUi', {
	xtype : 'MShop.panel.attribute.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'attribute' } } ] },
		listTypeKey : 'service/list/type/attribute'
	},
	listConfig : {
		domain : 'service',
		prefix : 'attribute.'
	}
}, 40);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

// hook media picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.MediaItemPickerUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'media' } } ] },
		listTypeKey : 'service/list/type/media'
	},
	listConfig : {
		domain : 'service',
		prefix : 'media.'
	}
}, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

// hook price picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.PriceItemPickerUi', {
	xtype : 'MShop.panel.price.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'price' } } ] },
		listTypeKey : 'service/list/type/price'
	},
	listConfig : {
		domain : 'service',
		prefix : 'price.'
	}
}, 30);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.autoExpandColumn = 'service-config-value';
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.service.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					Ext.each( that.getSelectionModel().getSelections(), function( selection, idx ) {
						that.store.remove(selection);
					}, this );

					var data = {};
					Ext.each( that.store.data.items, function( item, index ) {
						data[item.data.name] = item.data.value;
					}, this );
					
					that.data = data;
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: {
				width: 250,
				sortable: true
			},
			columns: [{
				id: 'service-config-name',
				header: _('Name'),
				dataIndex: 'name',
				editor: {
					xtype: 'textfield'
				}
			}, {
				id: 'service-config-value',
				header: _('Value'),
				dataIndex: 'value',
				editor: {
					xtype: 'textfield'
				}
			}]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.service.configui', MShop.panel.service.ConfigUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.plugin');

MShop.panel.plugin.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Plugin',
	idProperty : 'plugin.id',
	siteidProperty : 'plugin.siteid',
	itemUiXType : 'MShop.panel.plugin.itemui',

	autoExpandColumn : 'plugin-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'plugin.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Plugin');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.plugin.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		// make sure plugin type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Plugin_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'plugin.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'plugin.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'plugin.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "plugin.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'plugin.provider',
				header : _('Provider'),
				id : 'plugin-list-provider',
				sortable : true,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'plugin.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'plugin-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'plugin.position',
				header : _('Position'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'plugin.config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				renderer: function (value) {	
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'plugin.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'plugin.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'plugin.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}

} );

Ext.reg('MShop.panel.plugin.listui', MShop.panel.plugin.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.plugin.listui', MShop.panel.plugin.ListUi, 60);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.plugin');

MShop.panel.plugin.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,


	initComponent : function() {
		this.title = _('Plugin item details');
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.plugin.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.plugin.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'plugin.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'plugin.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'plugin.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Plugin_Type'),
							displayField : 'plugin.type.label',
							valueField : 'plugin.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Plugin type (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'plugin.type.code', 'order' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'textfield',
							fieldLabel : _('Provider'),
							name : 'plugin.provider',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Name of the plugin provider class (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'plugin.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal plugin name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Position'),
							name : 'plugin.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'plugin.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'plugin.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'plugin.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.plugin.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('plugin.config') : {} )
				}]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);

		MShop.panel.plugin.ItemUi.superclass.initComponent.call(this);
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['plugin.label'] : 'new';

		this.setTitle( 'Plugin: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},


	onBeforeSave: function( store, data ) {

		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.plugin.configui' );
		var first = editorGrid.shift();

		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['plugin.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['plugin.config'] = config;
		}
	}
});

Ext.reg('MShop.panel.plugin.itemui', MShop.panel.plugin.ItemUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.plugin');

MShop.panel.plugin.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.plugin.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					var selection = that.getSelectionModel().getSelections()[0];
					if (selection) {
						that.store.remove(selection);
						var data = {};
						Ext.each(that.store.data.items, function (item, index) {
							data[item.data.name] = item.data.value;
						}, this);
						that.data = data;
					}
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: { width: 250, sortable: true },
			columns: [
				{header: _('Name'), dataIndex: 'name', editor: { xtype: 'textfield'}},
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}}
			]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.plugin.configui', MShop.panel.plugin.ConfigUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order');

MShop.panel.order.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Order',
	idProperty : 'order.id',
	siteidProperty : 'order.siteid',
	itemUiXType : 'MShop.panel.order.itemui',

	sortInfo : {
		field : 'order.datepayment',
		direction : 'DESC'
	},

	autoExpandColumn : 'order-relatedid',

	filterConfig : {
		filters : [ {
			dataIndex : 'order.datepayment',
			operator : 'after',
			value : Ext.util.Format.date( new Date( new Date().valueOf() - 7 * 86400 * 1000 ), 'Y-m-d H:i:s' )
		} ]
	},

	initComponent : function()
	{
		this.title = _('Order');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.order.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.id',
				header : _('Id'),
				sortable : true,
				width : 55,
				id : 'order-list-id'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.type',
				header : _('Type/Source'),
				sortable : true,
				width : 85,
				align: 'center'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.datepayment',
				header : _('Purchase date'),
				sortable : true,
				width : 180,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.datedelivery',
				header : _('Delivery date'),
				sortable : true,
				width : 180,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.statuspayment',
				header : _('Payment status'),
				sortable : true,
				renderer: MShop.elements.paymentstatus.renderer
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.statusdelivery',
				header : _('Delivery status'),
				sortable : true,
				renderer: MShop.elements.deliverystatus.renderer
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.relatedid',
				header : _('Related Id'),
				id: 'order-relatedid'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.editor',
				header : _('Editor'),
				width : 130,
				sortable : true,
				editable : false,
				hidden : true
			}
		];
	},


    onOpenEditWindow: function(action) {
        if (action === 'add') {
            return Ext.Msg.alert(_('Not implemented'), _('Sorry, adding orders manually is currently not implemented'));
        }

        return MShop.panel.order.ListUi.superclass.onOpenEditWindow.apply(this, arguments);
    }
} );

Ext.reg('MShop.panel.order.listui', MShop.panel.order.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.order.listui', MShop.panel.order.ListUi, 40);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order');

MShop.panel.order.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',
	siteidProperty : 'order.siteid',

	initComponent : function() {
		this.title = _('Order item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.order.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.order.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Invoice',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'left',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _('ID'),
							name : 'order.id'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Source'),
							name : 'order.type'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Purchase date'),
							name : 'order.datepayment'
						}, {
							xtype : 'combo',
							fieldLabel : _('Payment status'),
							name: 'order.statuspayment',
							mode : 'local',
							store : MShop.elements.paymentstatus._store,
							displayField : 'label',
							valueField : 'value',
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Status (required)')
						}, {
							xtype : 'datefield',
							fieldLabel : 'Delivery date',
							name : 'order.datedelivery',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'combo',
							fieldLabel : _('Delivery status'),
							name: 'order.statusdelivery',
							mode : 'local',
							store : MShop.elements.deliverystatus._store,
							displayField : 'label',
							valueField : 'value',
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Status (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'order.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'order.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'order.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.order.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.order.itemui', MShop.panel.order.ItemUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order');

MShop.panel.order.OrderUi = Ext.extend(Ext.FormPanel, {
	
	recordName : 'Order_Base',
	idProperty : 'order.base.id',
	siteidProperty : 'order.base.siteid',

	title : 'Order',
	layout : 'fit',
	flex : 1,

	initComponent : function() {

		this.initStore();

		// get items
		this.items = [ {
			xtype : 'fieldset',
			style: 'padding-right: 25px;',
			border : false,
			labelAlign : 'left',
			defaults: {
				readOnly : this.fieldsReadOnly,
				anchor : '100%'
			},
			items : [ {
				xtype : 'displayfield',
				fieldLabel : _( 'Base ID' ),
				name : 'order.base.id'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Site'),
				name : 'order.base.sitecode'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Customer'),
				name : 'order.base.customerid'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Currency'),
				name : 'order.base.currencyid'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Total'),
				name : 'order.base.price'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Costs'),
				name : 'order.base.costs'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Comment'),
				name : 'order.base.comment'
			} ]
		} ];

		MShop.panel.order.OrderUi.superclass.initComponent.call(this);
	},

	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.OrderUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.OrderUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		this.getForm().loadRecord(this.record);
	},


	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.id' : this.itemUi.record.data['order.baseid']
				}
			} ]
		};

		return true;
	}
});

// hook this into the product ItemUi Basic Panel
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi.BasicPanel', 'MShop.panel.order.OrderUi', MShop.panel.order.OrderUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.product');

MShop.panel.order.base.product.ListUi = Ext.extend(Ext.Panel, {
	layout: 'fit',

	title : _('Products'),

	recordName : 'Order_Base_Product',

	idProperty : 'order.base.product.id',
	siteidProperty : 'order.base.product.siteid',
	itemUiXType : 'MShop.panel.order.product.itemui',

	autoExpandColumn : 'order-base-product-Label',

	gridConfig : null,

	storeConfig : null,

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',


	initComponent : function()
	{
		this.initStore();

		this.grid = new Ext.grid.GridPanel(Ext.apply({
			border: false,
			loadMask: true,
			store: this.store,
			autoExpandColumn: this.autoExpandColumn,
			columns: this.getColumns()
		}, this.gridConfig));

		this.items = [this.grid];
		this.grid.on('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);

		MShop.panel.order.base.product.ListUi.superclass.initComponent.call(this);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: _('No Items'),
				getRowClass: function (record, index){
					if (record.phantom === true) {
						return '';
					}

					var siteId = record.get(this.siteidProperty);

					if (siteId != MShop.config.site['locale.site.id']) {
						return this.rowCssClass;
					}

					return '';
				}.createDelegate(this)
			}
		});
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	},

	afterRender: function() {
		MShop.panel.order.base.product.ListUi.superclass.afterRender.apply(this, arguments);

		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		if (! this.store.autoLoad) {
			this.store.load.defer(50, this.store);
		}
	},

	onBeforeLoad: function(store, options) {

		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainFilter(store, options);
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.product.baseid' : this.ParentItemUi.record.data['order.baseid']
				}
			} ]
		};
	},

	onBeforeWrite: function(store, action, records, options) {
		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainProperty(store, action, records, options);
		}
	},

	onDestroy: function() {
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);
		this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);

		MShop.panel.order.base.product.ListUi.superclass.onDestroy.apply(this, arguments);
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = _('Error');
		var msg = response && response.error ? response.error.message : _('No error information available');
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function(store) {
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options) {
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if (! this.domainProperty) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push({'==': condition});
	},

	setDomainProperty: function(store, action, records, options) {
		var rs = [].concat(records);

		Ext.each(rs, function(record) {
			if (! this.domainProperty) {
				this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
			}
			record.data[this.domainProperty] = this.domain;
		}, this);
	},

	onOpenEditWindow: function(action) {
		var itemUi = Ext.ComponentMgr.create({
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : this.grid.getSelectionModel().getSelected(),
			store: this.store,
			listUI: this
		});

		itemUi.show();
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.id',
				header : _('Id'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.baseid',
				header : _('BaseId'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.productid',
				header : _('Product ID'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.orderproductid',
				header : _('Order Product ID'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.type',
				header : _('Type'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.prodcode',
				header : _('Code')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.name',
				header : _('Name'),
				id: 'order-base-product-Label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.quantity',
				header : _('Quantity')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.price',
				header : _('Price')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.costs',
				header : _('Costs')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.rebate',
				header : _('Discount')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.taxrate',
				header : _('Taxrate')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.status',
				header : _('Status'),
				renderer: MShop.elements.deliverystatus.renderer
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.product.listui', MShop.panel.order.base.product.ListUi);

//hook order base product into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.product.ListUi', MShop.panel.order.base.product.ListUi, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.address');

MShop.panel.order.base.address.ItemUi = Ext.extend(Ext.Panel, {

	recordName : 'Order_Base_Address',
	idProperty : 'order.base.address.id',
	siteidProperty : 'order.base.address.siteid',

	title : _('Addresses'),
	border : false,
	layout : 'hbox',
	layoutConfig : {
		align : 'stretch'
	},
	itemId : 'MShop.panel.order.base.address.ItemUi',
	plugins : [ 'ux.itemregistry' ],

	initComponent : function() {

		this.initStore();

		this.items = [ {
			xtype : 'form',
			title : 'Billing address',
			flex : 1,
			autoScroll : true,
			items : [ {
				xtype : 'fieldset',
				style: 'padding-right: 25px;',
				border : false,
				autoWidth : true,
				labelAlign : 'left',
				defaults: {
					anchor : '100%'
				},
				items : [ {
					xtype : 'displayfield',
					fieldLabel : _( 'ID' ),
					name : 'order.base.address.id'
				}, {
					xtype : 'displayfield',
					fieldLabel : _( 'Address ID' ),
					name : 'order.base.address.addressid'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Company',
					name: 'order.base.address.company'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Salutation',
					name: 'order.base.address.salutation'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Title',
					name: 'order.base.address.title'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Firstname',
					name: 'order.base.address.firstname'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Lastname',
					name: 'order.base.address.lastname'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Address 1',
					name: 'order.base.address.address1'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Address 2',
					name: 'order.base.address.address2'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Address 3',
					name: 'order.base.address.address3'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Postal code',
					name: 'order.base.address.postal'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'City',
					name: 'order.base.address.city'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'State',
					name: 'order.base.address.state'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Country',
					name: 'order.base.address.countryid'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Telephone',
					name: 'order.base.address.telephone'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Telefax',
					name: 'order.base.address.telefax'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'E-Mail',
					name: 'order.base.address.email'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Website',
					name: 'order.base.address.website'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Created'),
					name : 'order.base.address.ctime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Last modified'),
					name : 'order.base.address.mtime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Editor'),
					name : 'order.base.address.editor'
				} ]
			} ]
		} ];

		MShop.panel.order.base.address.ItemUi.superclass.initComponent.call(this);
	},

	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.address.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.base.address.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		var panelForm = this.findByType('form');
		panelForm[0].getForm().loadRecord(this.record);
	},

	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.address.baseid' : this.itemUi.record.data['order.baseid']
				}
			}, {
				'==' : {
					'order.base.address.type' : 'payment'
				}
			} ]
		};

		return true;
	}
});

Ext.reg('MShop.panel.order.base.address.itemui', MShop.panel.order.base.address.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.address.ItemUi', MShop.panel.order.base.address.ItemUi, 20);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.address');

MShop.panel.order.base.address.DeliveryItemUi = Ext.extend(Ext.FormPanel, {

	title : _('Delivery address'),
	flex: 1,
	autoScroll : true,
	recordName : 'Order_Base_Address',
	idProperty : 'order.base.address.id',
	siteidProperty : 'order.base.address.siteid',

	initComponent : function() {

		this.initStore();

		this.items = [ {
			xtype : 'fieldset',
			style: 'padding-right: 25px;',
			border : false,
			autoWidth : true,
			labelAlign : 'left',
			defaults: {
				anchor : '100%'
			},
			items : [ {
				xtype : 'displayfield',
				fieldLabel : _( 'ID' ),
				name : 'order.base.address.id'
			}, {
				xtype : 'displayfield',
				fieldLabel : _( 'Address ID' ),
				name : 'order.base.address.addressid'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Company',
				name: 'order.base.address.company'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Salutation',
				name: 'order.base.address.salutation'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Title',
				name: 'order.base.address.title'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Firstname',
				name: 'order.base.address.firstname'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Lastname',
				name: 'order.base.address.lastname'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Address 1',
				name: 'order.base.address.address1'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Address 2',
				name: 'order.base.address.address2'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Address 3',
				name: 'order.base.address.address3'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Postal code',
				name: 'order.base.address.postal'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'City',
				name: 'order.base.address.city'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'State',
				name: 'order.base.address.state'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Country',
				name: 'order.base.address.countryid'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Telephone',
				name: 'order.base.address.telephone'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Telefax',
				name: 'order.base.address.telefax'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'E-Mail',
				name: 'order.base.address.email'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Website',
				name: 'order.base.address.website'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Created'),
				name : 'order.base.address.ctime'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Last modified'),
				name : 'order.base.address.mtime'
			}, {
				xtype : 'displayfield',
				fieldLabel : _('Editor'),
				name : 'order.base.address.editor'
			} ]
		} ];

		MShop.panel.order.base.address.DeliveryItemUi.superclass.initComponent.call(this);
	},

	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.address.DeliveryItemUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.base.address.DeliveryItemUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		this.getForm().loadRecord(this.record);
	},

	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}
	
		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.address.baseid' : this.itemUi.record.data['order.baseid']
				}
			}, {
				'==' : {
					'order.base.address.type' : 'delivery'
				}
			} ]
		};
		
		return true;
	}
});

Ext.reg('MShop.panel.order.base.address.deliveryitemui', MShop.panel.order.base.address.DeliveryItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.base.address.ItemUi', 'MShop.panel.order.base.address.DeliveryItemUi', MShop.panel.order.base.address.DeliveryItemUi, 20);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.service.delivery');

MShop.panel.order.base.service.delivery.ItemUi = Ext.extend(Ext.Panel, {

	recordName : 'Order_Base_Service',
	idProperty : 'order.base.service.id',
	siteidProperty : 'order.base.service.siteid',

	title : _('Delivery'),
	border : false,
	layout : 'hbox',
	layoutConfig : {
		align : 'stretch'
	},
	itemId : 'MShop.panel.order.base.service.delivery.ItemUi',
	plugins : [ 'ux.itemregistry' ],

	initComponent : function() {

		this.initStore();

		this.items = [ {
			xtype : 'form',
			title : 'Details',
			flex : 1,
			autoScroll : true,
			items : [ {
				xtype : 'fieldset',
				style: 'padding-right: 25px;',
				border : false,
				labelAlign : 'left',
				defaults: {
					anchor : '100%'
				},
				items : [ {
					xtype : 'displayfield',
					fieldLabel : _( 'ID' ),
					name : 'order.base.service.id'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Service ID',
					name: 'order.base.service.serviceid'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Code',
					maxLength : 32,
					name: 'order.base.service.code'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Name',
					name: 'order.base.service.name'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Price',
					name: 'order.base.service.price'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Costs',
					name: 'order.base.service.costs'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Rebate',
					name: 'order.base.service.rebate'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Tax rate in %',
					name: 'order.base.service.taxrate'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Created'),
					name : 'order.base.service.ctime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Last modified'),
					name : 'order.base.service.mtime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Editor'),
					name : 'order.base.service.editor'
				} ]
			} ]
		} ];

		MShop.panel.order.base.service.delivery.ItemUi.superclass.initComponent.call(this);
	},

	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.service.delivery.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.base.service.delivery.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		var panelForm = this.findByType('form');
		panelForm[0].getForm().loadRecord(this.record);
	},

	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}
	
		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.service.baseid' : this.itemUi.record.data['order.baseid']
				}
			}, {
				'==' : {
					'order.base.service.type' : 'delivery'
				}
			} ]
		};
		
		return true;
	}
});

Ext.reg('MShop.panel.order.base.service.delivery.itemui', MShop.panel.order.base.service.delivery.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.service.delivery.ItemUi', MShop.panel.order.base.service.delivery.ItemUi, 30);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.service.delivery.attribute');

MShop.panel.order.base.service.delivery.attribute.ItemUi = Ext.extend(Ext.Panel, {

	title : _('Attributes'),
	flex : 1,
	layout: 'fit',

	recordName : 'Order_Base_Service_Attribute',

	idProperty : 'order.base.service.attribute.id',
	siteidProperty : 'order.base.service.attribute.siteid',

	gridConfig : null,

	storeConfig : null,

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',

	
	initComponent : function()
	{
		this.initStore();

		this.grid = new Ext.grid.GridPanel(Ext.apply({
			border: false,
			loadMask: true,
			store: this.store,
			autoExpandColumn: 'order-base-service-attribute-delivery-name-id',
			columns: this.getColumns()
		}, this.gridConfig));

		this.items = [this.grid];

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.initComponent.call(this);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: _('No Items'),
				getRowClass: function (record, index){
					if (record.phantom === true) {
						return '';
					}

					var siteId = record.get(this.siteidProperty);

					if (siteId != MShop.config.site['locale.site.id']) {
						return this.rowCssClass;
					}

					return '';
				}.createDelegate(this)
			}
		});
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	},

	afterRender: function() {
		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.order.base.service.delivery.ItemUi, false);
		});

		this.initRecord();

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	initRecord: function() {
		if (! this.ParentItemUi.record) {
			// wait till ref if here
			return this.initRecord.defer(50, this, arguments);
		}

		if (! this.store.autoLoad) {
			this.store.load();
		}
		return true;
	},

	onBeforeLoad: function(store, options) {

		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainFilter(store, options);
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.service.attribute.serviceid' : this.ParentItemUi.record.phantom ? null : this.ParentItemUi.record.data['order.base.service.id']
				}
			} ]
		};
	},

	onBeforeWrite: function(store, action, records, options) {
		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainProperty(store, action, records, options);
		}
	},

	onDestroy: function() {
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = _('Error');
		var msg = response && response.error ? response.error.message : _('No error information available');
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function(store) {
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options) {
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if (! this.domainProperty) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push({'==': condition});
	},

	setDomainProperty: function(store, action, records, options) {
		var rs = [].concat(records);

		Ext.each(rs, function(record) {
			if (! this.domainProperty) {
				this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
			}
			record.data[this.domainProperty] = this.domain;
		}, this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.id',
				header : _('ID'),
				width : 55,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.type',
				header : _('Type'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.name',
				header : _('Name'),
				id : 'order-base-service-attribute-delivery-name-id'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.code',
				header : _('Code'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.value',
				header : _('Value'),
				width : 150
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.editor',
				header : _('Editor'),
				width : 130,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.service.delivery.attribute.itemui', MShop.panel.order.base.service.delivery.attribute.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.base.service.delivery.ItemUi', 'MShop.panel.order.base.service.delivery.attribute.ItemUi', MShop.panel.order.base.service.delivery.attribute.ItemUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.service.payment');

MShop.panel.order.base.service.payment.ItemUi = Ext.extend(Ext.Panel, {

	recordName : 'Order_Base_Service',
	idProperty : 'order.base.service.id',
	siteidProperty : 'order.base.service.siteid',

	title : _('Payment'),
	border : false,
	layout : 'hbox',
	layoutConfig : {
		align : 'stretch'
	},
	itemId : 'MShop.panel.order.base.service.payment.ItemUi',
	plugins : [ 'ux.itemregistry' ],

	initComponent : function() {

		this.initStore();

		this.items = [ {
			xtype : 'form',
			title : 'Details',
			flex : 1,
			autoScroll : true,
			items : [ {
				xtype : 'fieldset',
				style: 'padding-right: 25px;',
				border : false,
				labelAlign : 'left',
				defaults: {
					anchor : '100%'
				},
				items : [ {
					xtype : 'displayfield',
					fieldLabel : _( 'ID' ),
					name : 'order.base.service.id'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Service ID',
					name: 'order.base.service.serviceid'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Code',
					maxLength : 32,
					name: 'order.base.service.code'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Name',
					name: 'order.base.service.name'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Price',
					name: 'order.base.service.price'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Costs',
					name: 'order.base.service.costs'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Rebate',
					name: 'order.base.service.rebate'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Tax rate in %',
					name: 'order.base.service.taxrate'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Created'),
					name : 'order.base.service.ctime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Last modified'),
					name : 'order.base.service.mtime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Editor'),
					name : 'order.base.service.editor'
				} ]
			} ]
		} ];

		MShop.panel.order.base.service.payment.ItemUi.superclass.initComponent.call(this);
	},
	
	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.service.payment.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.base.service.payment.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		var panelForm = this.findByType('form');
		panelForm[0].getForm().loadRecord(this.record);
	},

	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}
	
		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.service.baseid' : this.itemUi.record.data['order.baseid']
				}
			}, {
				'==' : {
					'order.base.service.type' : 'payment'
				}
			} ]
		};
		
		return true;
	}
});

Ext.reg('MShop.panel.order.base.service.payment.itemui', MShop.panel.order.base.service.payment.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.service.payment.ItemUi', MShop.panel.order.base.service.payment.ItemUi, 40);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.service.payment.attribute');

MShop.panel.order.base.service.payment.attribute.ItemUi = Ext.extend(Ext.Panel, {

	title : _('Attributes'),
	flex : 1,
	layout: 'fit',

	recordName : 'Order_Base_Service_Attribute',

	idProperty : 'order.base.service.attribute.id',
	siteidProperty : 'order.base.service.attribute.siteid',

	gridConfig : null,

	storeConfig : null,

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',


	initComponent : function()
	{
		this.initStore();

		this.grid = new Ext.grid.GridPanel(Ext.apply({
			border: false,
			loadMask: true,
			store: this.store,
			autoExpandColumn: 'order-base-service-attribute-payment-name-id',
			columns: this.getColumns()
		}, this.gridConfig));

		this.items = [this.grid];

		MShop.panel.order.base.service.payment.attribute.ItemUi.superclass.initComponent.call(this);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: _('No Items'),
				getRowClass: function (record, index){
					if (record.phantom === true) {
						return '';
					}

					var siteId = record.get(this.siteidProperty);

					if (siteId != MShop.config.site['locale.site.id']) {
						return this.rowCssClass;
					}

					return '';
				}.createDelegate(this)
			}
		});
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	},

	afterRender: function() {
		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.order.base.service.payment.ItemUi, false);
		});

		this.initRecord();

		MShop.panel.order.base.service.payment.attribute.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	initRecord: function() {
		if (! this.ParentItemUi.record) {
			// wait till ref if here
			return this.initRecord.defer(50, this, arguments);
		}

		if (! this.store.autoLoad) {
			this.store.load();
		}
		return true;
	},

	onBeforeLoad: function(store, options) {

		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainFilter(store, options);
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.service.attribute.serviceid' : this.ParentItemUi.record.phantom ? null : this.ParentItemUi.record.data['order.base.service.id']
				}
			} ]
		};
	},

	onBeforeWrite: function(store, action, records, options) {
		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainProperty(store, action, records, options);
		}
	},

	onDestroy: function() {
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.service.payment.attribute.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = _('Error');
		var msg = response && response.error ? response.error.message : _('No error information available');
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function(store) {
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options) {
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if (! this.domainProperty) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push({'==': condition});
	},

	setDomainProperty: function(store, action, records, options) {
		var rs = [].concat(records);

		Ext.each(rs, function(record) {
			if (! this.domainProperty) {
				this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
			}
			record.data[this.domainProperty] = this.domain;
		}, this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.id',
				header : _('ID'),
				width : 55,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.type',
				header : _('Type'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.name',
				header : _('Name'),
				width : 150,
				id : 'order-base-service-attribute-payment-name-id'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.code',
				header : _('Code'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.value',
				header : _('Value'),
				width : 150
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.editor',
				header : _('Editor'),
				width : 130,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.service.payment.attribute.itemui', MShop.panel.order.base.service.payment.attribute.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.base.service.payment.ItemUi', 'MShop.panel.order.base.service.payment.attribute.ItemUi', MShop.panel.order.base.service.payment.attribute.ItemUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.paymentstatus');

/**
 * @static
 * 
 * @return {String} label
 */
MShop.elements.paymentstatus.renderer = function(value) {
	var data = MShop.elements.paymentstatus._store.getAt( MShop.elements.paymentstatus._store.find('value', value) );

	if( data ) {
		return data.get('label');
	}
	
	return value;
};

/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.paymentstatus._store = new Ext.data.ArrayStore({
	idIndex : 0,
	fields : [
		{name: 'value', type: 'integer'},
		{name: 'label', type: 'string'}
	],
	data : [
		[-1, _('unfinished')],
		[0, _('deleted')],
		[1, _('canceled')],
		[2, _('refused')],
		[3, _('refund')],
		[4, _('pending')],
		[5, _('authorized')],
		[6, _('received')]
	]
});/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.deliverystatus');

/**
 * @static
 * 
 * @return {String} label
 */
MShop.elements.deliverystatus.renderer = function(value) {
	var data = MShop.elements.deliverystatus._store.getAt( MShop.elements.deliverystatus._store.find('value', value) );
	
	if( data ) {
		return data.get('label');
	}

	return value;
};

/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.deliverystatus._store = new Ext.data.ArrayStore({
	idIndex : 0,
	fields : [
		{name: 'value', type: 'integer'},
		{name: 'label', type: 'string'}
	],
	data : [
		[-1, _('unfinished')],
		[0, _('deleted')],
		[1, _('pending')],
		[2, _('progress')],
		[3, _('dispatched')],
		[4, _('delivered')],
		[5, _('lost')],
		[6, _('refused')],
		[7, _('returned')]
	]
});

/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.order.product' );

MShop.panel.order.product.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'order.base.product.siteid',

	initComponent : function() {

		this.title = _('Product item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.order.product.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Product' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.order.product.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'left',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'order.base.product.id'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Product ID' ),
							name : 'order.base.product.productid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Order Product ID' ),
							name : 'order.base.product.orderproductid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Type' ),
							name : 'order.base.product.type'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Code' ),
							name : 'order.base.product.prodcode'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Name' ),
							name : 'order.base.product.name'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Quantity' ),
							name : 'order.base.product.quantity'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Price' ),
							name : 'order.base.product.price'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Costs' ),
							name : 'order.base.product.costs'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Rebate' ),
							name : 'order.base.product.rebate'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Tax rate in %' ),
							name : 'order.base.product.taxrate'
						}, {
							xtype : 'ux.formattabledisplayfield',
							fieldLabel : _( 'Status' ),
							name : 'order.base.product.status',
							renderer : MShop.elements.deliverystatus.renderer
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'order.base.product.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'order.base.product.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'order.base.product.editor'
						} ]
					} ]
				},{
					xtype: 'MShop.panel.order.base.product.attribute.listuismall',
					layout: 'fit',
					flex: 1,
					onOpenEditWindow: function(){}
				} ]
			} ]
		} ];

		MShop.panel.order.product.ItemUi.superclass.initComponent.call( this );
	}
});

Ext.reg( 'MShop.panel.order.product.itemui', MShop.panel.order.product.ItemUi );
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.order.base.product.attribute' );

MShop.panel.order.base.product.attribute.ListUiSmall = Ext.extend( MShop.panel.AbstractListUi, {

	title: _( 'Attribute' ),
	recordName : 'Order_Base_Product_Attribute',
	idProperty : 'order.base.product.attribute.id',
	siteidProperty : 'order.base.product.attribute.siteid',
	itemUiXType : 'MShop.panel.order.product.itemui',

	sortInfo : {
		field : 'order.base.product.attribute.id',
		direction : 'ASC'
	},

	autoExpandColumn : 'order-base-product-attribute-name',

	filterConfig : {
		filters : [ {
			dataIndex : 'order.base.product.attribute.code',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function() {
		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.initComponent.apply( this, arguments );

		this.grid.un('rowcontextmenu', this.onGridContextMenu, this);
		this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);
	},

	initToolbar: function() {
		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.initToolbar.apply( this, arguments );
		this.tbar = [];
	},

	afterRender : function() {
		this.itemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.afterRender.apply( this, arguments );
	},

	onBeforeLoad: function( store, options ) {
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.product.attribute.productid' : this.itemUi.record ? this.itemUi.record.id : null
				}
			} ]
		};

	},

	getColumns : function()
	{
		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.id',
				header : _( 'ID' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.type',
				header : _('Type'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.name',
				header : _('Name'),
				id : 'order-base-product-attribute-name'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.code',
				header : _( 'Code' ),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.value',
				header : _( 'Value' ),
				width : 150
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.attribute.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.attribute.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.editor',
				header : _('Editor'),
				width : 130,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.order.base.product.attribute.listuismall', MShop.panel.order.base.product.attribute.ListUiSmall );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.job');

MShop.panel.job.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,

	initComponent : function() {

		this.title = _('Job item details');

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.job.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.job.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'job.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'job.status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'job.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Job label (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Method'),
							name : 'job.method'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'job.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'job.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'job.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.job.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.job.itemui', MShop.panel.job.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.job');

MShop.panel.job.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Admin_Job',
	idProperty : 'job.id',
	siteidProperty : 'job.siteid',
	itemUiXType : 'MShop.panel.job.itemui',

	sortInfo : {
		field : 'job.ctime',
		direction : 'DESC'
	},

	autoExpandColumn : 'job-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'job.ctime',
			operator : 'after',
			value : Ext.util.Format.date( new Date( new Date().valueOf() - 7 * 86400 * 1000 ), 'Y-m-d H:i:s' )
		} ]
	},

	initComponent : function()
	{
		this.title = _('Job');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.job.ListUiSmall.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'job.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.label',
				header : _('Label'),
				sortable : true,
				editable : false,
				id : 'job-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.method',
				header : _('Method'),
				sortable : true,
				width : 200,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.parameter',
				header : _('Parameter'),
				sortable : false,
				width : 100,
				editable : false,
				hidden : true,
				renderer : function( data ) {
					try {
						var result = '';
						var object = Ext.decode( data );

						for( var name in object ) {
							result += name + ': ' + object[name] + '<br/>';
						}
						return result;
					} catch( e ) {
						return data;
					}
				}
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.result',
				header : _('Result'),
				sortable : false,
				width : 200,
				editable : false,
				renderer : function( data ) {
					try {
						var result = '';
						var object = Ext.decode( data );

						if ( object instanceof Array ) {
							return '';
						}

						for( var name in object ) {
							result += name + ': ' + object[name] + '<br/>';
						}
						return result;
					} catch( e ) {
						return data;
					}
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'job.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'job.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.editor',
				header : _('Editor'),
				sortable : true,
				width : 80,
				editable : false
			}
		];
	}
} );

Ext.reg('MShop.panel.job.listuismall', MShop.panel.job.ListUiSmall);

// hook this into the main tab panel
// Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', MShop.panel.job.ListUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.log' );

MShop.panel.log.ListUiSmall = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Admin_Log',
	idProperty : 'log.id',
	siteidProperty : 'log.siteid',
	itemUiXType : null,

	sortInfo : {
		field : 'log.timestamp',
		direction : 'DESC'
	},

	autoExpandColumn : 'list-log-message',

	filterConfig : {
		filters : [ {
			dataIndex : 'log.timestamp',
			operator : 'after',
			value : Ext.util.Format.date( new Date( new Date().valueOf() - 86400 * 1000 ), 'Y-m-d H:i:s' )
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Admin Log' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.log.ListUiSmall.superclass.initComponent.call( this );

		this.grid.un( 'rowcontextmenu', this.onGridContextMenu, this );
		this.grid.un( 'rowdblclick', this.onOpenEditWindow.createDelegate( this, ['edit'] ), this );
		this.grid.getSelectionModel().un( 'selectionchange', this.onGridSelectionChange, this, {buffer: 10} );
		this.actionAdd.setDisabled( 1 );
	},

	onOpenEditWindow: function(action) {
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'log.id',
				header : _( 'Id' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.facility',
				header : _( 'Facility' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'log.timestamp',
				header : _( 'Date' ),
				width : 130,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.priority',
				header : _( 'Priority' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.message',
				header : _( 'Message' ),
				id: 'list-log-message',
				renderer : function( data ) {
					try {
						var result = '';
						var object = Ext.decode( data );

						for( var name in object ) {
							result += name + ': ' + object[name] + '<br/>'; 
						}
						return '<div style="white-space:normal !important;">' + result + '</div>';
					} catch( e ) {
						return '<div style="white-space:normal !important;">' + data + '</div>';
					}
				}
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.request',
				header : _( 'Request' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'log.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.log.listuismall', MShop.panel.log.ListUiSmall );
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.stock.warehouse' );

MShop.panel.stock.warehouse.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Product_Stock_Warehouse',
	idProperty : 'product.stock.warehouse.id',
	siteidProperty : 'product.stock.warehouse.siteid',
	itemUiXType : 'MShop.panel.stock.warehouse.itemui',

	autoExpandColumn : 'product-warehouse-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.stock.warehouse.code',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Warehouse' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.stock.warehouse.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		this.typeStore = MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse' );

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.id',
				header : _( 'Id' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.status',
				header : _( 'Status' ),
				sortable : true,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate( this )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.code',
				header : _( 'Code' ),
				sortable : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.label',
				header : _( 'Label' ),
				sortable : true,
				id : 'product-warehouse-list-label'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.warehouse.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.warehouse.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.stock.warehouse.listui', MShop.panel.stock.warehouse.ListUi );

Ext.ux.ItemRegistry.registerItem( 'MShop.MainTabPanel', 'MShop.panel.stock.warehouse.listui', MShop.panel.stock.warehouse.ListUi, 90 );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.stock.warehouse' );

MShop.panel.stock.warehouse.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {
	
	recordName : 'Product_Stock_Warehouse',
	idProperty : 'product.stock.warehouse.id',
	siteidProperty : 'product.stock.warehouse.siteid',

	initComponent : function() {

		this.title = _( 'Warehouse' );

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.stock.warehouse.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.stock.warehouse.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.stock.warehouse.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'product.stock.warehouse.status'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Warehouse code',
							name : 'product.stock.warehouse.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _( 'Warehouse code (required)' )
						}, {
							xtype : 'textfield',
							fieldLabel : 'Warehouse label',
							name : 'product.stock.warehouse.label',
							allowBlank : false,
							emptyText : _( 'Warehouse label (required)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.stock.warehouse.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.stock.warehouse.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.stock.warehouse.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.stock.warehouse.ItemUi.superclass.initComponent.call( this );
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['product.stock.warehouse.label'] : 'new';
		this.setTitle( 'Warehouse: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.stock.warehouse.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg( 'MShop.panel.stock.warehouse.itemui', MShop.panel.stock.warehouse.ItemUi );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.locale' );

MShop.panel.locale.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Locale',
	idProperty : 'locale.id',
	siteidProperty : 'locale.siteid',
	itemUiXType : 'MShop.panel.locale.itemui',

	sortInfo : {
		field : 'locale.position',
		direction : 'ASC'
	},

	autoExpandColumn : 'locale-currencyid',

	filterConfig : {
		filters : [ {
			dataIndex : 'locale.position',
			operator : 'greaterequals',
			value : 0
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Locale' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.locale.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'locale.id',
				header : _('ID'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.languageid',
				header : _('Language ID'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.currencyid',
				header : _('Currency ID'),
				sortable : true,
				width : 100,
				id : 'locale-currencyid'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.position',
				header : _('Position'),
				sortable : true,
				width : 50
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.locale.listui', MShop.panel.locale.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.locale.listui', MShop.panel.locale.ListUi, 80);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.locale' );

MShop.panel.locale.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	recordName : 'Locale',
	idProperty : 'locale.id',
	siteidProperty : 'locale.siteid',

	initComponent : function()
	{
		this.title = _('Locale item details');
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.locale.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.locale.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'locale.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'locale.status'
						}, {
							xtype : 'MShop.elements.language.combo',
							fieldLabel : _( 'Language' ),
							name : 'locale.languageid',
							allowBlank : false,
							emptyText : _( 'Language (required)' )
						}, {
							xtype : 'MShop.elements.currency.combo',
							fieldLabel : _( 'Currency' ),
							name : 'locale.currencyid',
							allowBlank : false,
							emptyText : _( 'Currency (required)' )
						}, {
							xtype : 'numberfield',
							name : 'locale.position',
							fieldLabel : 'Position',
							allowNegative : false,
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'locale.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'locale.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'locale.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.locale.ItemUi.superclass.initComponent.call( this );
	},

	afterRender : function()
	{
		this.setTitle( this.title + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	}
} );

Ext.reg( 'MShop.panel.locale.itemui', MShop.panel.locale.ItemUi );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.locale.site' );

MShop.panel.locale.site.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Locale_Site',
	idProperty : 'locale.site.id',
	siteidProperty : 'locale.site.id',
	itemUiXType : 'MShop.panel.locale.site.itemui',

	autoExpandColumn : 'locale-site-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'locale.site.label',
			operator : 'startswith',
			value : 0
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Locale Site' );
		
		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		this.initStore();

		MShop.panel.locale.site.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.id',
				header : _('ID'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.code',
				header : _('Code'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'locale-site-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				renderer: function (value) {
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.site.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.site.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	},
	
	initToolbar: function() {
		this.tbar = [
			this.actionAdd,
			this.actionEdit,
			this.actionDelete,
			this.actionExport,
			this.importButton
		];
	},
	
	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read    : MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].insertItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	}
} );

Ext.reg('MShop.panel.locale.site.listui', MShop.panel.locale.site.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.locale.site.listui', MShop.panel.locale.site.ListUi, 80);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.locale.site' );

MShop.panel.locale.site.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	recordName : 'Locale_Site',
	idProperty : 'locale.site.id',
	siteidProperty : 'locale.site.id',

	initComponent : function()
	{
		this.title = _('Locale site item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.locale.site.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.locale.site.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'locale.site.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'locale.site.status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'locale.site.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('Unique site code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'locale.site.label',
							allowBlank : false,
							emptyText : _('Internal site name (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'locale.site.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'locale.site.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'locale.site.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.locale.site.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('locale.site.config') : {} )
				} ]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);
		
		MShop.panel.locale.site.ItemUi.superclass.initComponent.call( this );
	},

	afterRender : function()
	{
		this.setTitle( this.title + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.locale.site.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	onBeforeSave: function( store, data ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.locale.site.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['locale.site.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['locale.site.config'] = config;
		}
	},
	
	onSaveItem: function() {
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false )
		{
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false )
		{
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.record.beginEdit();
		this.record.set( 'locale.site.label', this.mainForm.getForm().findField( 'locale.site.label' ).getValue() );
		this.record.set( 'locale.site.status', this.mainForm.getForm().findField( 'locale.site.status' ).getValue() );
		this.record.set( 'locale.site.code', this.mainForm.getForm().findField( 'locale.site.code' ).getValue() );
		this.record.endEdit();

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	}
} );




Ext.reg( 'MShop.panel.locale.site.itemui', MShop.panel.locale.site.ItemUi );/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.locale.site');

MShop.panel.locale.site.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,
	autoExpandColumn : 'locale-site-config-value',

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.locale.site.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					var selection = that.getSelectionModel().getSelections()[0];
					if (selection) {
						that.store.remove(selection);
						var data = {};
						Ext.each(that.store.data.items, function (item, index) {
							data[item.data.name] = item.data.value;
						}, this);
						that.data = data;
					}
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: { width: 250, sortable: true },
			columns: [
				{header: _('Name'), dataIndex: 'name', editor: { xtype: 'textfield'}},
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}, id:'locale-site-config-value'}
			]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.locale.site.configui', MShop.panel.locale.site.ConfigUi);/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.text.type');

MShop.panel.text.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	// Data Record (MShop_Text_Type like db. just without MShop)
	recordName : 'Text_Type',
	idProperty : 'text.type.id',
	siteidProperty : 'text.type.siteid',
	itemUiXType : 'MShop.panel.text.type.itemui',
	
	// Sort by id ASC
	sortInfo : {
		field : 'text.type.id',
		direction : 'ASC'
	},

	// Create filter
	filterConfig : {
		filters : [ {
			dataIndex : 'text.type.label',
			operator : 'startswith',
			value : ''
		} ]
	},
	
	// Override initComponent to set Label of tab.
	initComponent : function()
	{
		this.title = _('Text Type');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.text.type.ListUi.superclass.initComponent.call(this);
	},
	
	
	autoExpandColumn : 'text-type-label',

	getColumns : function() {
		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.id',
				header : _('ID'),
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.domain',
				header : _('Domain'),
				sortable : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.code',
				header : _('Code'),
				sortable : true,
				width : 150,
				align: 'center',
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.label',
				id: 'text-type-label',
				header : _('Label'),
				sortable : true,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'text.type.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'text.type.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.text.type.listui', MShop.panel.text.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.text.type.listui', MShop.panel.text.type.ListUi, 10);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.text.type');

MShop.panel.text.type.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'text.type.siteid',

	initComponent : function() {
		this.title = _('Text type details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.text.type.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.text.type.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							anchor : '100%',
							readOnly : this.fieldsReadOnly
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'text.type.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'text.type.status',
							allowBlank : false
						}, {
							xtype : 'MShop.elements.domain.combo',
							name : 'text.type.domain',
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'text.type.code',
							fieldLabel : _('Code'),
							emptyText : _('Code (required)'),
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'text.type.label',
							fieldLabel : _('Label'),
							emptyText : _('Label (required)'),
							allowBlank : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'text.type.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'text.type.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'text.type.editor'
						}]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.text.type.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['text.type.label'] : 'new';

		this.setTitle( 'Text Type: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.text.type.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.text.type.itemui', MShop.panel.text.type.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.media.type');

MShop.panel.media.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	// Data Record (MShop_Media_Type like db. just without MShop)
	recordName : 'Media_Type',
	idProperty : 'media.type.id',
	siteidProperty : 'media.type.siteid',
	
	itemUiXType : 'MShop.panel.media.type.itemui',
	
	// Sort by id ASC
	sortInfo : {
		field : 'media.type.id',
		direction : 'ASC'
	},

	// Create filter
	filterConfig : {
		filters : [ {
			dataIndex : 'media.type.label',
			operator : 'startswith',
			value : ''
		} ]
	},
	
	// Override initComponent to set Label of tab.
	initComponent : function()
	{
		this.title = _('Media Type');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.media.type.ListUi.superclass.initComponent.call(this);
	},
	
	
	autoExpandColumn : 'media-type-label',

	getColumns : function() {
		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.id',
				header : _('ID'),
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.domain',
				header : _('Domain'),
				sortable : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.code',
				header : _('Code'),
				sortable : true,
				width : 150,
				align: 'center',
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.label',
				id: 'media-type-label',
				header : _('Label'),
				sortable : true,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'media.type.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'media.type.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'media.type.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.media.type.listui', MShop.panel.media.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.media.type.listui', MShop.panel.media.type.ListUi, 20);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.media.type');

MShop.panel.media.type.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
	
	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'media.type.siteid',

	initComponent : function() {
		this.title = _('Media type details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.media.type.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.media.type.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							anchor : '100%',
							readOnly : this.fieldsReadOnly
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'media.type.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'media.type.status',
							allowBlank : false
						}, {
							xtype : 'MShop.elements.domain.combo',
							name : 'media.type.domain',
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'media.type.code',
							fieldLabel : _('Code'),
							emptyText : _('Code (required)'),
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'media.type.label',
							fieldLabel : _('Label'),
							emptyText : _('Label (required)'),
							allowBlank : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'media.type.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'media.type.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'media.type.editor'
						}]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.media.type.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['media.type.label'] : 'new';

		this.setTitle( 'Media Type: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.media.type.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.media.type.itemui', MShop.panel.media.type.ItemUi);
/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.type');

/**
 * Nest all types in a types tab
 * 
 * @todo abstract this if useful
 * @class MShop.panel.type.TabUi
 * @extends Ext.Panel
 */
MShop.panel.type.TabUi = Ext.extend(Ext.Panel, {

	maximized : true,
	layout : 'fit',
	modal : true,

	initComponent : function() {

		this.title = _('Types');

		this.items = [{
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.type.tabUi',
			plugins : [ 'ux.itemregistry' ]
		}];

		MShop.panel.type.TabUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.type.tabui', MShop.panel.type.TabUi);

Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.type.tabui', MShop.panel.type.TabUi, 120);