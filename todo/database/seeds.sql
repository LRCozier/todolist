-- Insert sample tasks
INSERT INTO tasks (user_id, title, description, completed) 
VALUES 
    (1, 'Buy groceries', 'Milk, eggs, bread', FALSE),
    (1, 'Finish project', 'Vue.js + PHP todo app', TRUE),
    (1, 'Call mom', NULL, FALSE);
