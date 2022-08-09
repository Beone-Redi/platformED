CREATE VIEW 
view_company_users AS 
SELECT a.fullname, c.company, b.idcard, a.perfil, a.active 
FROM en_users AS a
LEFT JOIN en_card AS b ON b.ide = a.idcard
LEFT JOIN en_company AS c ON c.ide = a.company
WHERE a.company = '1';