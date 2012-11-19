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

    File: layout.tpl
*}
<!DOCTYPE html>
<html lang="{$langvars['l_lang_attribute']}">
<!-- START OF HEADER -->
  <head>
    <meta charset="utf-8">
    <meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
    <meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
    <meta name="Rating" content="General">
    <link rel="shortcut icon" href="images/bntfavicon.ico">
    <link rel="stylesheet" type="text/css" href="{$template_dir}/styles/main.css.php">
    <link rel="stylesheet" type="text/css" href="{$template_dir}/styles/{$variables['body_class']}.css.php">
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ubuntu">
    <title>{block name=title}Default Page Title{/block}</title>
    <script src="{$template_dir}/javascript/ckeditor/ckeditor.js"></script>
  </head>
<!-- END OF HEADER -->

{if !isset($variables['body_class'])}
{$variables['body_class'] = "bnt"}
{/if}
  <body class="{$variables['body_class']}">
<div class="wrapper">

<!-- START OF BODY -->
{block name=body_title}{/block}
{block name=body}{/block}
<!-- END OF BODY -->

<!-- START OF FOOTER -->
<div class="push"></div></div>
<div class="footer">
{if isset($news)}
<br>
<script type="text/javascript" src="{$template_dir}/javascript/newsticker.js.php"></script>
<div id="news_ticker" class="faderlines" style="width:602px; margin:auto; text-align:center;">News Ticker should be here unless you have broken it!</div>
<script>
// News Ticker Constructor.
news = new newsTicker();

// I have put in some safaty precautions, but just in case always check the return value from initTicker().
if (news.initTicker("news_ticker") == true)
{
    // Set the width of the Ticker (in pixles)
    news.Width(500);

    // Sets the Interval/Update Time in seconds.
    news.Interval(5);

    // I have decided on adding single news articles at a time due to it makes it more easier to add when using PHP or XSL.
    // We can supply the information by either of the following ways:
    // 1: Supply the information from a Database and inserting it with PHP.
    // 2: Supply the information from a Database and convert it into XML (for formatting) and have the XSLT Stylesheet extract the information and insert it.
{* Cycle through the player list *}
{foreach $news as $article}
    news.addArticle('{$article['url']}', '{$article['text']}', '{$article['type']}', {$article['delay']});
{/foreach}

    // Starts the Ticker.
    news.startTicker();

    // If for some reason you need to stop the Ticker use the following line.
    // news.stopTicker();
}
</script>

{/if}
<br>
{* Handle the Servers Update Ticker here *}
{if isset($variables['update_ticker']['display']) && $variables['update_ticker']['display'] == true}
    <script type='text/javascript' src='{$template_dir}/javascript/updateticker.js.php'></script>
    <script>
        var seconds = {$variables['update_ticker']['seconds_left']};
        var nextInterval = new Date().getTime();
        var maxTicks = ({$variables['update_ticker']['sched_ticks']} * 60);
        var l_running_update = '{$langvars['l_running_update']}';
        var l_footer_until_update = '{$langvars['l_footer_until_update']}';

        setTimeout("NextUpdate();", 100);
    </script>
    <div style="width:600px; margin:auto; text-align:center;"><span id=update_ticker>{$langvars['l_please_wait']}</span></div>
{/if}
{* End of Servers Update Ticker *}

    <div style='clear:both'></div>
    <div style="text-align:center">
      <div style="width:600px; margin:auto; text-align:center;">
{* Handle the Online Players Counter *}
{if isset($variables['players_online']) && $variables['players_online'] == 1}
{$langvars['l_footer_one_player_on']}
{else}
{$langvars['l_footer_players_on_1']} {$variables['players_online']} {$langvars['l_footer_players_on_2']}
{/if}
{* End of Online Players Counter *}
      </div>
<br>
    </div>

{if $variables['suppress_logo'] == false}
    <div style='position:absolute; float:left; text-align:left'><a href='http://www.sourceforge.net/projects/blacknova'><img style="border:none;" width="{$variables['sf_logo_width']}" height="{$variables['sf_logo_height']}" src="http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type={$variables['sf_logo_type']}" alt="Blacknova Traders at SourceForge.net"></a></div>
{/if}
    <div style="font-size:smaller; text-align:right"><a class="new_link" href="news.php{$variables['sf_logo_link']}">{$langvars['l_local_news']}</a></div>
    <div style='font-size:smaller; text-align:right'>&copy; 2000-{$variables['cur_year']} Ron Harwood &amp; the BNT Dev team</div>

{if isset($variables['footer_show_debug']) && $variables['footer_show_debug'] == true}
    <div style="font-size:smaller; text-align:right">{$variables['elapsed']} {$langvars['l_seconds']} {$langvars['l_time_gen_page']} / {$variables['mem_peak_usage']} {$langvars['l_peak_mem']}</div>
{/if}
</div>
<!-- END OF FOOTER -->

  </body>
</html>
