// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Javascript to handle actions with the grading.
 *
 * @module     mod_assignexternal/grading_actions
 * @copyright  2024 Marcel Suter <marcel@ghwalin.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.1
 */
export const init = () => {
    document.getElementById('id_submit').addEventListener('click', submitAction);
    document.getElementById('selectAll').addEventListener('change', selectAll);
};

/**
 * Submit all selected userids
 */
function submitAction() {
    const fields = document.getElementsByName('selectbox');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const coursemoduleid = urlParams.get('id');
    let userids = '';
    for (let i = 0; i < fields.length; i++) {
        if (fields[i].type === 'checkbox' && fields[i].checked) {
            userids += '&uid[]=' + fields[i].getAttribute('data-userid');
        }
    }
    window.location.href = './view.php?action=override&id=' + coursemoduleid + userids;
}

/**
 * Check or uncheck all checkboxes
 */
function selectAll() {
    const ischecked = document.getElementById('selectAll').checked;
    const fields = document.getElementsByName('selectbox');
    for (let i = 0; i < fields.length; i++) {
        if (fields[i].type === 'checkbox') {
            fields[i].checked = ischecked;
        }
    }
}