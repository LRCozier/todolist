-- Insert a test user (password: "demo123")
INSERT INTO users (email, password_hash) 
VALUES (
    'demo@example.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' -- bcrypt hash of "demo123"
);

-- Insert sample tasks
INSERT INTO tasks (user_id, title, description, completed) 
VALUES 
    (1, 'Buy groceries', 'Milk, eggs, bread', FALSE),
    (1, 'Finish project', 'Vue.js + PHP todo app', TRUE),
    (1, 'Call mom', NULL, FALSE);
