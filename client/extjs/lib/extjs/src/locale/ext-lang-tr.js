/*
This file is part of Ext JS 3.4

Copyright (c) 2011-2013 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as
published by the Free Software Foundation and appearing in the file LICENSE included in the
packaging of this file.

Please review the following information to ensure the GNU General Public License version 3.0
requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department
at http://www.sencha.com/contact.

Build date: 2013-04-03 15:07:25
*/
/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * Turkish translation by Alper YAZGAN
 * 2008-01-24, 10:29 AM 
 * 
 * Updated to 2.2 by YargicX
 * 2008-10-05, 06:22 PM
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Yükleniyor ...</div>';

if(Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext.grid.Grid){
  Ext.grid.Grid.prototype.ddText = "Seçili satır sayısı : {0}";
}

if(Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = "Sekmeyi kapat";
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "Bu alandaki deðer geçersiz";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Yükleniyor ...";
}

Date.monthNames = [
  "Ocak",
  "Þžubat",
  "Mart",
  "Nisan",
  "Mayıs",
  "Haziran",
  "Temmuz",
  "Aðustos",
  "Eylül",
  "Ekim",
  "Kasım",
  "Aralık"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  "Pazar",
  "Pazartesi",
  "Salı",
  "Ç‡arþŸamba",
  "PerþŸembe",
  "Cuma",
  "Cumartesi"
];

Date.shortDayNames = [
  "Paz",
  "Pzt",
  "Sal",
  "ÇrþŸ",
  "Prþ",
  "Cum",
  "Cmt"
];

Date.getShortDayName = function(day) {
  return Date.shortDayNames[day];
};

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "Tamam",
    cancel : "Ä°ptal",
    yes    : "Evet",
    no     : "Hayır"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "d/m/Y");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Bugün",
    minText           : "Bu tarih izin verilen en küçük tarihten daha önce",
    maxText           : "Bu tarih izin verilen en büyük tarihten daha sonra",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'Gelecek Ay (Control+Right)',
    prevText          : 'Ã–nceki Ay (Control+Left)',
    monthYearText     : 'Bir ay sŸeçiniz (Yılı artırmak/azaltmak için Control+Up/Down)',
    todayTip          : "{0} (BoþŸluk TuþŸu - Spacebar)",
    format            : "d/m/Y",
    okText            : "&#160;Tamam&#160;",
    cancelText        : "Ä°ptal",
    startDay          : 1
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Sayfa",
    afterPageText  : " / {0}",
    firstText      : "Ä°lk Sayfa",
    prevText       : "Ã–nceki Sayfa",
    nextText       : "Sonraki Sayfa",
    lastText       : "Son Sayfa",
    refreshText    : "Yenile",
    displayMsg     : "Gösterilen {0} - {1} / {2}",
    emptyMsg       : 'Gösterilebilecek veri yok'
  });
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "Girilen verinin uzunluðu en az {0} olabilir",
    maxLengthText : "Girilen verinin uzunluðu en fazla {0} olabilir",
    blankText     : "Bu alan boþŸ bırakılamaz",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : "En az {0} girilebilir",
    maxText : "En çok {0} girilebilir",
    nanText : "{0} geçersiz bir sayıdır"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "Disabled",
    disabledDatesText : "Disabled",
    minText           : "Bu tarih, {0} tarihinden daha sonra olmalıdır", 
    maxText           : "Bu tarih, {0} tarihinden daha önce olmalıdır",
    invalidText       : "{0} geçersiz bir tarihdir - tarih formatı {1} þŸeklinde olmalıdır",
    format            : "d/m/Y",
    altFormats        : "d.m.y|d.m.Y|d/m/y|d-m-Y|d-m-y|d.m|d/m|d-m|dm|dmY|dmy|d|Y.m.d|Y-m-d|Y/m/d",
    startDay          : 1
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Yükleniyor ...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
	Ext.form.VTypes["emailText"]='Bu alan "user@example.com" þŸeklinde elektronik posta formatında olmalıdır';
	Ext.form.VTypes["urlText"]='Bu alan "http://www.example.com" þŸeklinde URL adres formatında olmalıdır';
	Ext.form.VTypes["alphaText"]='Bu alan sadece harf ve _ içermeli';
	Ext.form.VTypes["alphanumText"]='Bu alan sadece harf, sayı ve _ içermeli';
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Lütfen bu baðlantı için gerekli URL adresini giriniz:',
    buttonTips : {
      bold : {
        title: 'Kalın(Bold) (Ctrl+B)',
        text: 'Þžeçili yazıyı kalın yapar.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Ä°talik(Italic) (Ctrl+I)',
        text: 'Þžeçili yazıyı italik yapar.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Alt Ã‡izgi(Underline) (Ctrl+U)',
        text: 'Þžeçili yazının altını çizer.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Fontu büyült',
        text: 'Yazı fontunu büyütür.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Fontu küçült',
        text: 'Yazı fontunu küçültür.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Arka Plan Rengi',
        text: 'Seçili yazının arka plan rengini deðiþŸtir.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Yazı Rengi',
        text: 'Seçili yazının rengini deðiþŸtir.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Sola Daya',
        text: 'Yazıyı sola daya.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Ortala',
        text: 'Yazıyı editörde ortala.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Saða daya',
        text: 'Yazıyı saða daya.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Noktalı Liste',
        text: 'Noktalı listeye baþŸla.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Numaralı Liste',
        text: 'Numaralı lisyeye baþŸla.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Web Adresi(Hyperlink)',
        text: 'Seçili yazıyı web adresi(hyperlink) yap.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Kaynak kodu Düzenleme',
        text: 'Kaynak kodu düzenleme moduna geç.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "Artan sırada sırala",
    sortDescText : "Azalan sırada sırala",
    lockText     : "Kolonu kilitle",
    unlockText   : "Kolon kilidini kaldır",
    columnsText  : "Kolonlar"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Yok)',
    groupByText    : 'Bu Alana Göre Grupla',
    showGroupsText : 'Gruplar Halinde Göster'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Ad",
    valueText  : "Deðer",
    dateFormat : "d/m/Y"
  });
}

if(Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Yeniden boyutlandırmak için sürükle.",
    collapsibleSplitTip : "Yeniden boyutlandırmak için sürükle. Saklamak için çift tıkla."
  });
}
