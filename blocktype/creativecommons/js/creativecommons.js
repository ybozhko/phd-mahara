/**
 * Automatic display of the Free Culture seal
 * @source: http://gitorious.org/mahara/mahara
 *
 * @licstart
 * Copyright (C) 2009-2010  Catalyst IT Ltd
 *
 * The JavaScript code in this page is free software: you can
 * redistribute it and/or modify it under the terms of the GNU
 * General Public License (GNU GPL) as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option)
 * any later version.  The code is distributed WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.
 *
 * As additional permission under GNU GPL version 3 section 7, you
 * may distribute non-source (e.g., minimized or compacted) forms of
 * that code without the copy of the GNU GPL normally required by
 * section 4, provided you include this license notice and a URL
 * through which recipients can access the Corresponding Source.
 * @licend
 */

function toggle_seal() {
    freeculture = true;
    sealimage = $("freecultureseal");

    nc_checkboxes = getElementsByTagAndClassName("input", null, $("instconf_noncommercial_container"));
    if (!nc_checkboxes[0].checked) {
        freeculture = false;
    }

    nd_checkboxes = getElementsByTagAndClassName("input", null, $("instconf_noderivatives_container"));
    if (nd_checkboxes[2].checked) {
        freeculture = false;
    }

    if (freeculture) {
        removeElementClass(sealimage, "hidden");
    }
    else {
        addElementClass(sealimage, "hidden");
    }
}
