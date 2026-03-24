import sqlite3

conn = sqlite3.connect('database/database.sqlite')
cur = conn.cursor()
cur.execute('SELECT id, connection, queue, payload, exception, failed_at FROM failed_jobs ORDER BY id DESC LIMIT 20')
rows = cur.fetchall()
if not rows:
    print('failed_jobs table is empty')
else:
    print('latest failed_jobs:')
    for r in rows:
        print('---')
        print('id:', r[0])
        print('connection:', r[1])
        print('queue:', r[2])
        print('failed_at:', r[5])
        print('exception:')
        print(r[4])
        print('payload snippet:')
        p = r[3] or ''
        print((p[:1000] + '...' if len(p) > 1000 else p))
conn.close()
