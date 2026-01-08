-- TypeMaster Seed Data
-- Word lists and achievements

USE typemaster_db;

-- Easy Words (Common 200+ words)
INSERT INTO text_sets (content, difficulty, category, language, word_count) VALUES
('the be to of and a in that have I it for not on with he as you do at this but his by from they we say her she or an will my one all would there their what so up out if about who get which go me when make can like time no just him know take people into year your good some could them see other than then now look only come its over think also back after use two how our work first well way even new want because any these give day most us', 'easy', 'common', 'en', 100),
('home hand high year mother found read keep house never seem state same world life still work own just place come use right most did good look back feel say way think great day see us take too want because find new get make any well each work last very after school just through even before long turn small right part home left three point old also number world off good little', 'easy', 'common', 'en', 80),
('city water move light grow learn letter earth keep school thought head under story city feet saw left know far hand house side head food stand own page should country found answer school study still learn point father mother paper great music family ask room between sun hand door during hold answer picture change different move answer much boy such turn here why old because does animal house', 'easy', 'common', 'en', 75),
('run walk talk listen play work read write think learn grow move stand sit jump fly swim climb fall rise sleep wake eat drink cook clean wash drive ride build make create design develop plan start stop begin end finish complete open close push pull carry hold drop catch throw take give send receive buy sell pay cost save spend earn money time day night morning evening', 'easy', 'common', 'en', 70),
('happy sad angry calm excited bored tired awake hungry full cold warm bright dark quiet loud fast slow high low big small old new long short wide narrow deep shallow heavy light soft hard smooth rough wet dry clean dirty rich poor young early late first last easy hard simple complex single double triple open closed public private safe dangerous', 'easy', 'common', 'en', 65);

-- Medium Difficulty (With punctuation)
INSERT INTO text_sets (content, difficulty, category, language, word_count) VALUES
('The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs. How vexingly quick daft zebras jump! The five boxing wizards jump quickly. Sphinx of black quartz, judge my vow. Two driven jocks help fax my big quiz. The jay, pig, fox, zebra, and my wolves quack!', 'medium', 'punctuation', 'en', 55),
('Hello, world! How are you doing today? I hope everything is going well. The weather is beautiful, isn''t it? Let''s go for a walk in the park. Don''t forget to bring your umbrella, just in case. It might rain later this afternoon. See you soon!', 'medium', 'punctuation', 'en', 50),
('Programming is the art of telling a computer what to do. It requires logic, creativity, and patience. "Code is poetry," as they say. Every bug is a learning opportunity. Debug, test, repeat. That''s the developer''s mantra. Keep coding!', 'medium', 'punctuation', 'en', 45),
('"To be, or not to be: that is the question." Shakespeare wrote those famous words centuries ago. Literature has the power to move us, inspire us, and change our perspectives. What''s your favorite book? Mine is "1984" by George Orwell.', 'medium', 'punctuation', 'en', 45),
('Email: contact@example.com | Phone: (555) 123-4567 | Address: 123 Main St., Suite 100, New York, NY 10001. Visit us online at www.example.com. We''re open Monday-Friday, 9:00 AM - 5:00 PM EST. Questions? Don''t hesitate to reach out!', 'medium', 'punctuation', 'en', 45);

-- Hard Difficulty (With numbers and symbols)
INSERT INTO text_sets (content, difficulty, category, language, word_count) VALUES
('The meeting is scheduled for 3:45 PM on 12/25/2024. Please bring documents #A-123, #B-456, and #C-789. The budget is $50,000 (fifty thousand dollars). Contact us at support@company.com or call 1-800-555-0199. Reference code: XYZ_2024_Q4.', 'hard', 'numbers', 'en', 40),
('function calculateTotal(items) { let sum = 0; for (let i = 0; i < items.length; i++) { sum += items[i].price * items[i].quantity; } return sum.toFixed(2); } // Returns: "$125.99"', 'hard', 'code', 'en', 35),
('Password requirements: 8+ characters, 1 uppercase (A-Z), 1 lowercase (a-z), 1 number (0-9), and 1 special character (!@#$%^&*). Example: "Secure#Pass123" or "MyP@ssw0rd!" - Never use "password123" or "qwerty"!', 'hard', 'numbers', 'en', 40),
('Math equations: (x + y) * z = 42, where x = 10, y = 4, z = 3. Calculate: 15% of $200 = $30. The formula is: E = mc^2. Pi (π) ≈ 3.14159. The square root of 144 is 12. 2^10 = 1024.', 'hard', 'numbers', 'en', 45),
('SELECT * FROM users WHERE age >= 18 AND status = "active" ORDER BY created_at DESC LIMIT 100; -- SQL query | UPDATE products SET price = price * 1.10 WHERE category_id IN (1, 2, 3);', 'hard', 'code', 'en', 35);

-- Code snippets
INSERT INTO text_sets (content, difficulty, category, language, word_count) VALUES
('const fetchData = async (url) => { try { const response = await fetch(url); const data = await response.json(); return data; } catch (error) { console.error("Error:", error); throw error; } };', 'hard', 'code', 'en', 30),
('class User { constructor(name, email) { this.name = name; this.email = email; this.createdAt = new Date(); } greet() { return `Hello, ${this.name}!`; } }', 'hard', 'code', 'en', 25),
('def quicksort(arr): if len(arr) <= 1: return arr pivot = arr[len(arr) // 2] left = [x for x in arr if x < pivot] middle = [x for x in arr if x == pivot] right = [x for x in arr if x > pivot] return quicksort(left) + middle + quicksort(right)', 'hard', 'code', 'en', 50);

-- Quotes and Sentences
INSERT INTO text_sets (content, difficulty, category, language, word_count) VALUES
('Success is not final, failure is not fatal: it is the courage to continue that counts. The only way to do great work is to love what you do. Innovation distinguishes between a leader and a follower. Stay hungry, stay foolish.', 'medium', 'quotes', 'en', 45),
('In the middle of difficulty lies opportunity. Life is what happens when you are busy making other plans. The greatest glory in living lies not in never falling, but in rising every time we fall. Be the change you wish to see in the world.', 'medium', 'quotes', 'en', 50),
('It does not matter how slowly you go as long as you do not stop. The future belongs to those who believe in the beauty of their dreams. Believe you can and you are halfway there. The only impossible journey is the one you never begin.', 'medium', 'quotes', 'en', 50);

-- Achievements
INSERT INTO achievements (name, description, icon, requirement_type, requirement_value, requirement_operator, rarity, xp_reward) VALUES
-- WPM Achievements
('Speed Demon', 'Reach 50 WPM in a test', '⚡', 'wpm', 50, '>=', 'common', 10),
('Velocity Master', 'Reach 75 WPM in a test', '🚀', 'wpm', 75, '>=', 'rare', 25),
('Lightning Fingers', 'Reach 100 WPM in a test', '💨', 'wpm', 100, '>=', 'epic', 50),
('Keyboard Warrior', 'Reach 125 WPM in a test', '⚔️', 'wpm', 125, '>=', 'legendary', 100),
('The Flash', 'Reach 150 WPM in a test', '✨', 'wpm', 150, '>=', 'legendary', 200),

-- Accuracy Achievements
('Precision Starter', 'Complete a test with 95% accuracy', '🎯', 'accuracy', 95, '>=', 'common', 10),
('Sharp Shooter', 'Complete a test with 98% accuracy', '🏹', 'accuracy', 98, '>=', 'rare', 25),
('Perfectionist', 'Complete a test with 100% accuracy', '💎', 'accuracy', 100, '=', 'epic', 50),

-- Test Count Achievements
('First Steps', 'Complete your first typing test', '👶', 'tests', 1, '>=', 'common', 5),
('Getting Warmed Up', 'Complete 10 typing tests', '🔥', 'tests', 10, '>=', 'common', 15),
('Dedicated Typist', 'Complete 50 typing tests', '💪', 'tests', 50, '>=', 'rare', 30),
('Typing Veteran', 'Complete 100 typing tests', '🎖️', 'tests', 100, '>=', 'rare', 50),
('Typing Master', 'Complete 500 typing tests', '👑', 'tests', 500, '>=', 'epic', 100),
('Typing Legend', 'Complete 1000 typing tests', '🏆', 'tests', 1000, '>=', 'legendary', 200),

-- Streak Achievements
('On Fire', 'Maintain a 3-day streak', '🔥', 'streak', 3, '>=', 'common', 10),
('Week Warrior', 'Maintain a 7-day streak', '📅', 'streak', 7, '>=', 'rare', 25),
('Monthly Master', 'Maintain a 30-day streak', '📆', 'streak', 30, '>=', 'epic', 75),
('Year of Dedication', 'Maintain a 365-day streak', '🌟', 'streak', 365, '>=', 'legendary', 500),

-- Time Achievements
('Quick Session', 'Type for 5 minutes total', '⏱️', 'time', 300, '>=', 'common', 5),
('Practice Makes Perfect', 'Type for 1 hour total', '⏰', 'time', 3600, '>=', 'rare', 25),
('Marathon Typist', 'Type for 10 hours total', '🏃', 'time', 36000, '>=', 'epic', 75),
('Typing Addict', 'Type for 100 hours total', '💻', 'time', 360000, '>=', 'legendary', 200),

-- Special Achievements
('Night Owl', 'Complete a test after midnight', '🦉', 'special', 1, '>=', 'rare', 15),
('Early Bird', 'Complete a test before 6 AM', '🐦', 'special', 1, '>=', 'rare', 15),
('Challenge Accepted', 'Complete a daily challenge', '🎮', 'challenge', 1, '>=', 'common', 20),
('Challenge Champion', 'Complete 30 daily challenges', '🏅', 'challenge', 30, '>=', 'epic', 100);

-- Initialize Leaderboard Cache
INSERT INTO leaderboard_cache (type, test_mode, data) VALUES
('global', 'all', '[]'),
('daily', 'all', '[]'),
('weekly', 'all', '[]'),
('monthly', 'all', '[]');
