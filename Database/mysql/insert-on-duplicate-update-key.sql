Insert …. on  duplicate key update
Insert into test(col1, col2, col3)
	Values (1,2,3),
(4,5,6),
(7,5,9)
on  duplicate key update 
col2 =values(col2),
col3 = values(2) + values(4)
