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
 * Javascript to handle changing users via the user selector in the header.
 *
 * @module     mod_assignexternal/grading_navigation
 * @copyright  2024 Marcel Suter <marcel@ghwalin.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.1
 */
import {fetchAllStudents} from './repository';
export const init = () => {
    loadAllStudents().then(() => {
        document.getElementById('user_autocomplete_downarrow').addEventListener('click', toggleUserlist);
        document.getElementById('user_autocomplete_input').addEventListener('input', filterUserlist);
        document.getElementById('user_autocomplete_suggestions').addEventListener('click', selectUser);
        document.getElementById('previous-user').addEventListener('click', navigateUser);
        document.getElementById('next-user').addEventListener('click', navigateUser);
        return true;
    })
        .catch((error) => {
            window.alert('Oops!' + error);
        });
};

/**
 * loads all students for the current course
 * @returns {Promise<void>}
 */
const loadAllStudents = async() => {
    const courseid = document.getElementsByName('courseid')[0].value;
    const response = await fetchAllStudents(courseid);
    let dropdown = document.getElementById('change-user-select');
    let datalist = document.getElementById('user_autocomplete_suggestions');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const currentId = urlParams.get('userid');
    for (let i= 0; i < response.length; i++) {
        addStudents(response[i], dropdown, datalist, currentId);
    }
};

/**
 * Adds a student to the dropdown and datalist
 * @param {object} student the student
 * @param {dom} dropdown
 * @param {dom} datalist
 * @param {int} currentStudentId
 */
function addStudents(student, dropdown, datalist, currentStudentId) {
    let option = document.createElement('option');
    option.text = student.firstname + ' ' + student.lastname;
    option.value = student.userid;
    dropdown.add(option);

    let item = document.createElement('li');
    item.id = 'user_autocomplete_suggestion_' + student.userid;
    item.innerHTML = student.firstname + ' ' + student.lastname + ' <small>' + student.email + '</small>';
    item.setAttribute('data-value', student.userid);
    if (student.userid == currentStudentId) {
        item.setAttribute('aria-selected', 'true');
        datalist.setAttribute('data-currentItem', datalist.childElementCount);
    }
    item.setAttribute('role', 'option');
    datalist.appendChild(item);
}

/**
 * toggles show/noshow for the userlist
 */
function toggleUserlist() {
    let userlist = document.getElementById('user_autocomplete_suggestions');
    if (userlist.style.display === 'none') {
        userlist.style.display = 'inline';
        document.getElementById('user_autocomplete_input').focus();
    } else {
        userlist.style.display = 'none';
    }
}

/**
 * filter the userlist
 */
function filterUserlist() {
    const filter = document.getElementById('user_autocomplete_input').value.toUpperCase();
    const userlist = document.getElementById('user_autocomplete_suggestions');
    const users = userlist.getElementsByTagName('li');
    userlist.style.display = 'inline';
    for (const user of users) {
        const value = user.innerText.toUpperCase();
        if (filter === '' || value.includes(filter)) {
            user.style.display = 'block';
        } else {
            user.style.display = 'none';
        }
    }
}

/**
 * gets the list item that was clicked
 * @param {Object} event  the event
 */
function selectUser(event) {
    let element = event.target;
    if (event.target) {
        if (event.target.nodeName === 'SMALL') {
            element = event.target.parentNode;
        }
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        urlParams.set('userid', element.getAttribute('data-value'));
        window.location.href = '?' + urlParams.toString();
    }

}

/**
 * navigates to the next or previous user
 * @param {Object} event
 */
function navigateUser(event) {
    const datalist = document.getElementById('user_autocomplete_suggestions');
    const children = datalist.getElementsByTagName('LI');
    let currentItem = datalist.getAttribute('data-currentitem');
    if (event.target.id === 'previous-user') {
        if (currentItem === '0') {
            currentItem = children.length - 1;
        } else {
            currentItem--;
        }
    } else {
        if (currentItem >= children.length - 1) {
            currentItem = 0;
        } else {
            currentItem++;
        }
    }
    const nextNode = children[currentItem];
    const userid = nextNode.getAttribute('data-value');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    urlParams.set('userid', userid);
    window.location.href = '?' + urlParams.toString();
}