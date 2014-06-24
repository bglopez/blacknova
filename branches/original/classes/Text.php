<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: classes/Text.php

namespace Bnt;

class Text
{
    public static function gotoMain($db, $lang, $langvars)
    {
        $langvars = Translate::load($db, $lang, array ('global_funcs', 'common'));
        echo str_replace('[here]', "<a href='main.php'>" . $langvars['l_here'] . '</a>', $langvars['l_global_mmenu']);
    }
}
?>