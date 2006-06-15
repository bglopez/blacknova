<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: smarty.php
// Todo: Add support for variable scratch dir settings.
// Todo: Add commenting for each setting.

class bnt_smarty extends Smarty
{
    function bnt_smarty()
    {
        // Class constructor

        $this->Smarty();
        $this->security = true; // Enforce tight security for templates: http://smarty.php.net/manual/en/variable.security.php
        $this->compile_dir = ('../scratch_dir');
        $this->cache_dir = ('../scratch_dir');
    }
}

?>
