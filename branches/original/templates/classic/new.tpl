{*
    Blacknova Traders - A web-based massively multiplayer space combat and trading game
    Copyright (C) 2001-2012 Ron Harwood and the BNT development team.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    File: index.tpl
*}

{extends file="layout.tpl"}
{block name=title}{$langvars['l_welcome_bnt']}{/block}

{block name=body}
<div class="index-header"><img height="150" width="994" style="width:100%" class="index" src="templates/{$variables['template']}/images/header1.png" alt="{$langvars['l_bnt']}"></div>

<div class="index-header-text">{$langvars['l_bnt']}</div>
<br>
<div class="index-welcome">
<h1 style='text-align:center'>{$langvars['l_new_title']}</h1>
<form action="new2.php{$variables['link']}" method="post">
    <dl class='twocolumn-form'>
        <dt style='padding:3px'><label for='username'>{$langvars['l_login_email']}:</label></dt>
        <dd style='padding:3px'><input type='email' id='username' name='username' size='20' maxlength='40' value='' placeholder='someone@example.com' style='width:200px'></dd>
        <dt style='padding:3px'><label for='shipname'>{$langvars['l_new_shipname']}:</label></dt>
        <dd style='padding:3px'><input type='text' id='shipname' name='shipname' size='20' maxlength='20' value='' style='width:200px'></dd>
        <dt style='padding:3px'><label for='character'>{$langvars['l_new_pname']}:</label></dt>
        <dd style='padding:3px'><input type='text' id='character' name='character' size='20' maxlength='20' value='' style='width:200px'></dd>
        <dt style='padding:3px'><label for='password'>{$langvars['l_login_pw']}:</label></dt>
        <dd style='padding:3px'><input type='password' id='password' name='password' size='20' maxlength='20' value='' style='width:200px'></dd>
        <dt style='padding:3px'><label for='newlang'>{$langvars['l_opt_lang']}:</label></dt>
        <dd style='padding:3px'><select name=newlang>
        {for $i=0 to count($variables['lang_name']) -1}
            {if $variables['lang_file'][$i] == $variables['selected_lang']}
                <option value='{$variables['lang_file'][$i]}' selected>{$variables['lang_name'][$i]}</option>
            {else}
                <option value='{$variables['lang_file'][$i]}'>{$variables['lang_name'][$i]}</option>
            {/if}
        {/for}
        </select></dd>
    </dl>
<br style="clear:both">
<div style="text-align:center">
<span class="button green"><a class="nocolor" href="#" onclick="document.forms[0].submit();return false;"><span class="shine"></span>{$langvars['l_submit']}</a></span>
<span class="button red"><a class="nocolor" href="#" onclick="document.forms[0].reset();return false;"><span class="shine"></span>{$langvars['l_reset']}</a></span>
<div style="width: 0; height: 0; overflow: hidden;"><input type="submit" value="{$langvars['l_submit']}"></div> 
</div>
</form>
<br>
        {$langvars['l_new_info']}<br></div>
<br>
{/block}
