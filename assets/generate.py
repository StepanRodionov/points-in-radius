import MySQLdb
import settings
from scipy.stats import logistic as logist

lat = logist.rvs(loc=55.75, scale=0.07, size=settings.set_size, random_state=32)
lon = logist.rvs(loc=37.62, scale=0.04, size=settings.set_size, random_state=32)
many_coords = [(float(lat[x]), float(lon[x])) for x in range(0, len(lat))]

conn = MySQLdb.connect(db=settings.db, host=settings.host, port=settings.port, user=settings.user, passwd=settings.passwd)
cursor = conn.cursor()

insert = """insert into points(lat, lon) values (%s, %s)"""
cursor.executemany(insert, many_coords)

cursor.close()
conn.commit()