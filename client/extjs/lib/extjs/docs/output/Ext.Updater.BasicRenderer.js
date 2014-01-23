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
Ext.data.JsonP.Ext_Updater_BasicRenderer({"alternateClassNames":[],"aliases":{},"enum":null,"parentMixins":[],"tagname":"class","subclasses":[],"extends":null,"uses":[],"html":"<div><pre class=\"hierarchy\"><h4>Files</h4><div class='dependency'><a href='source/UpdateManager.html#Ext-Updater-BasicRenderer' target='_blank'>UpdateManager.js</a></div></pre><div class='doc-contents'><p>This class is a base class implementing a simple render method which updates an element using results from an Ajax request.</p>\n\n\n<p>The BasicRenderer updates the element's innerHTML with the responseText. To perform a custom render (i.e. XML or JSON processing),\ncreate an object with a conforming <a href=\"#!/api/Ext.Updater.BasicRenderer-method-render\" rel=\"Ext.Updater.BasicRenderer-method-render\" class=\"docClass\">render</a> method and pass it to setRenderer on the Updater.</p>\n\n</div><div class='members'><div class='members-section'><div class='definedBy'>Defined By</div><h3 class='members-title icon-method'>Methods</h3><div class='subsection'><div id='method-render' class='member first-child not-inherited'><a href='#' class='side expandable'><span>&nbsp;</span></a><div class='title'><div class='meta'><span class='defined-in' rel='Ext.Updater.BasicRenderer'>Ext.Updater.BasicRenderer</span><br/><a href='source/UpdateManager.html#Ext-Updater-BasicRenderer-method-render' target='_blank' class='view-source'>view source</a></div><a href='#!/api/Ext.Updater.BasicRenderer-method-render' class='name expandable'>render</a>( <span class='pre'>el, xhr, updateManager, callback</span> )</div><div class='description'><div class='short'>This method is called when an Ajax response is received, and an Element needs updating. ...</div><div class='long'><p>This method is called when an Ajax response is received, and an Element needs updating.</p>\n<h3 class=\"pa\">Parameters</h3><ul><li><span class='pre'>el</span> : <a href=\"#!/api/Ext.Element\" rel=\"Ext.Element\" class=\"docClass\">Ext.Element</a><div class='sub-desc'><p>The element being rendered</p>\n</div></li><li><span class='pre'>xhr</span> : Object<div class='sub-desc'><p>The XMLHttpRequest object</p>\n</div></li><li><span class='pre'>updateManager</span> : Updater<div class='sub-desc'><p>The calling update manager</p>\n</div></li><li><span class='pre'>callback</span> : <a href=\"#!/api/Function\" rel=\"Function\" class=\"docClass\">Function</a><div class='sub-desc'><p>A callback that will need to be called if loadScripts is true on the Updater</p>\n</div></li></ul></div></div></div></div></div></div></div>","superclasses":[],"meta":{},"requires":[],"html_meta":{},"statics":{"property":[],"cfg":[],"css_var":[],"method":[],"event":[],"css_mixin":[]},"files":[{"href":"UpdateManager.html#Ext-Updater-BasicRenderer","filename":"UpdateManager.js"}],"linenr":511,"members":{"property":[],"cfg":[],"css_var":[],"method":[{"tagname":"method","owner":"Ext.Updater.BasicRenderer","meta":{},"name":"render","id":"method-render"}],"event":[],"css_mixin":[]},"inheritable":null,"private":null,"component":false,"name":"Ext.Updater.BasicRenderer","singleton":false,"override":null,"inheritdoc":null,"id":"class-Ext.Updater.BasicRenderer","mixins":[],"mixedInto":[]});