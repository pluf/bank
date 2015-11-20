package ir.co.dpq.pluf;

import java.sql.SQLException;
import java.util.List;

import com.j256.ormlite.dao.Dao;
import com.j256.ormlite.stmt.PreparedQuery;
import com.j256.ormlite.stmt.QueryBuilder;

import ir.co.dpq.pluf.km.PCategory;
import ir.co.dpq.pluf.km.PLabel;
import ir.co.dpq.pluf.user.PUser;
import ir.co.dpq.pluf.wiki.IPWikiBookDao;
import ir.co.dpq.pluf.wiki.PWikiBook;
import ir.co.dpq.pluf.wiki.PWikiPage;
import ir.co.dpq.pluf.wiki.PWikiPageItem;

/**
 * 
 * @author maso
 *
 */
public class PWikiBookDaoOrmlitJdbc implements IPWikiBookDao {

	private Dao<PWikiBook, Long> wikiDao;

	/*
	 * (non-Javadoc)
	 * 
	 * @see
	 * ir.co.dpq.pluf.wiki.IPWikiBookDao#createWikiBook(ir.co.dpq.pluf.wiki.
	 * PWikiBook)
	 */
	@Override
	public PWikiBook createWikiBook(PWikiBook book) {
		try {
			if (book.getId() == null || book.getId() == 0) {
				book.setId(System.currentTimeMillis());
			}
			PWikiBook rbook = wikiDao.createIfNotExists(book);
			return rbook;
		} catch (SQLException e) {
			throw new PException("error", e);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see ir.co.dpq.pluf.wiki.IPWikiBookDao#getWikiBook(java.lang.Long)
	 */
	@Override
	public PWikiBook getWikiBook(Long bookId) {
		try {
			PWikiBook rbook = wikiDao.queryForId(bookId);
			return rbook;
		} catch (SQLException e) {
			throw new PException("error", e);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see
	 * ir.co.dpq.pluf.wiki.IPWikiBookDao#updateWikiBook(ir.co.dpq.pluf.wiki.
	 * PWikiBook)
	 */
	@Override
	public PWikiBook updateWikiBook(PWikiBook book) {
		try {
			wikiDao.update(book);
			return book;
		} catch (SQLException e) {
			throw new PException("error", e);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see
	 * ir.co.dpq.pluf.wiki.IPWikiBookDao#deleteWikiBook(ir.co.dpq.pluf.wiki.
	 * PWikiBook)
	 */
	@Override
	public PWikiBook deleteWikiBook(PWikiBook book) {
		try {
			wikiDao.delete(book);
			book.setId(0l);
			return book;
		} catch (SQLException e) {
			throw new PException("error", e);
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see ir.co.dpq.pluf.wiki.IPWikiBookDao#findWikiBook(ir.co.dpq.pluf.
	 * PPaginatorParameter)
	 */
	@Override
	public IPPaginatorPage<PWikiBook> findWikiBook(PPaginatorParameter param) {
		QueryBuilder<PWikiBook, Long> queryBuilder = wikiDao.queryBuilder();
		try {
			// count
			Long count = queryBuilder.countOf();

			// Items
			queryBuilder = wikiDao.queryBuilder();
			queryBuilder//
					.limit((long) param.getItemPerPage())//
					.offset((long) param.getPage() * param.getItemPerPage());
			PreparedQuery<PWikiBook> preparedQuery = queryBuilder.prepare();
			List<PWikiBook> list = wikiDao.query(preparedQuery);

			PPaginatedWikiBook page = new PPaginatedWikiBook();
			page.setItemsPerPage(param.getItemPerPage())//
					.setItems(list)//
					.setCurrentPage(param.getPage())//
					.setPageNumber(count.intValue() / param.getItemPerPage()
							+ ((count.intValue() % param.getItemPerPage() != 0) ? 1 : 0));
			return page;
		} catch (SQLException e) {
			throw new PException(e.getMessage(), e);
		}
	}

	@Override
	public PWikiBook addLabelToBook(PWikiBook book, PLabel label) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public List<PLabel> getBookLabels(PWikiBook book) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiBook deleteLabelFromBook(PWikiBook book, PLabel label) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage addCategoryToBook(PWikiBook book, PCategory category) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage deleteCategoryFromBook(PWikiBook book, PCategory category) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public List<PCategory> getBookCategories(PWikiBook book) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public IPPaginatorPage<PWikiPageItem> getBookPages(PWikiBook book, PPaginatorParameter param) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage addPageToBook(PWikiBook book, PWikiPage page) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage deletePageFromBook(PWikiBook book, PWikiPage page) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage addInterestedUser(PWikiBook book) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public PWikiPage deleteInterestedUser(PWikiBook book) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public IPPaginatorPage<PUser> getBookInteresteds(PWikiBook book, PPaginatorParameter param) {
		// TODO Auto-generated method stub
		return null;
	}

	public void setWikiDao(Dao<PWikiBook, Long> wikiDao) {
		this.wikiDao = wikiDao;
	}

}