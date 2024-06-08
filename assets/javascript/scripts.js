document.addEventListener('DOMContentLoaded', () => {
    const PHP_PATH = `${window.location.origin}/php/functions/`;
    let categoryFromUrl = new URLSearchParams(window.location.search).get('category') || 'Alle taken';
    fetchTasksByCategory(categoryFromUrl);

    document.querySelectorAll('.nav-link-category').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const category = event.target.getAttribute('data-category');
            fetchTasksByCategory(category);

            document.querySelectorAll('.nav-link-category').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');

            document.querySelector('h1').textContent = category;

            const newUrl = `${window.location.pathname}?category=${category}`;
            window.history.pushState({path: newUrl}, '', newUrl);
        });
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    function fetchTasksByCategory(category) {
        fetch(`${PHP_PATH}fetch_tasks.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({category})
        })
            .then(response => response.json())
            .then(data => {
                const taskList = document.querySelector('.list-group');
                if (taskList) {
                    taskList.innerHTML = '';
                    if (data.length === 0) {
                        taskList.innerHTML = '<li class="list-group-item">Geen taken gevonden</li>';
                    } else {
                        data.forEach(task => {
                            const listItem = document.createElement('li');
                            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                            listItem.innerHTML = `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="${task.id}">
                                    <label class="form-check-label" for="${task.id}">
                                        <strong>${task.task}</strong><br>
                                        <span>${task.description}</span><br>
                                        <small>Deadline: ${formatDate(task.deadline)}</small>
                                    </label>
                                </div>
                            `;
                            taskList.appendChild(listItem);

                            listItem.querySelector('.form-check-input').addEventListener('change', function () {
                                updateTaskStatus(this.id, this.checked);
                            });
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Fout bij het ophalen van taken:', error);
            });
    }

    function updateTaskStatus(taskId, completed) {
        const params = new URLSearchParams();
        params.append('taskId', taskId);
        params.append('completed', completed);

        fetch(`${PHP_PATH}complete.php`, {
            method: 'POST',
            body: params
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Taakstatus succesvol bijgewerkt.');
                } else {
                    console.error('Fout bij het bijwerken van de taakstatus. Foutmelding:', data.error);
                }
            })
            .catch(error => {
                console.error('Fout bij het uitvoeren van het AJAX-verzoek:', error);
            });
    }

    document.getElementById('addTaskButton').addEventListener('click', function () {
        const title = document.getElementById('taskTitle').value;
        const description = document.getElementById('taskDescription').value;
        let category = document.getElementById('taskCategory').value;
        const deadline = document.getElementById('taskDeadline').value;

        if (!category) {
            const newCategory = document.getElementById('newCategory').value.trim();
            if (!newCategory) {
                console.error('Geen categorie geselecteerd of ingevoerd.');
                return;
            }
            category = newCategory;
        }

        fetch(`${PHP_PATH}add_task.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title,
                description,
                category,
                deadline
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));
                    modal.hide();

                    document.getElementById('addTaskForm').reset();

                    console.log('Taak succesvol toegevoegd.');
                } else {
                    console.error('Fout bij het toevoegen van de taak:', data.error);
                }
            })
            .catch(error => {
                console.error('Fout bij het uitvoeren van het AJAX-verzoek:', error);
            });
    });

    document.getElementById('openAddTaskModal').addEventListener('click', function () {
        document.getElementById('taskCategory').value = document.querySelector('h1').textContent;
    });

    const newCategoryCheckbox = document.getElementById('newCategoryCheckbox');
    const newCategoryInput = document.getElementById('newCategoryInput');

    newCategoryCheckbox.addEventListener('change', function () {
        if (this.checked) {
            newCategoryInput.style.display = 'block';
        } else {
            newCategoryInput.style.display = 'none';
        }
    });
});
