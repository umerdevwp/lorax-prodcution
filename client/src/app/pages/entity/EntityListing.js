import React, {useContext, useEffect} from 'react';
import Link from '@material-ui/core/Link';
import Table from '@material-ui/core/Table';
import TableHead from '@material-ui/core/TableHead';
import TableContainer from '@material-ui/core/TableContainer';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableFooter from '@material-ui/core/TableFooter';
import TablePagination from '@material-ui/core/TablePagination';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import IconButton from '@material-ui/core/IconButton';
import FirstPageIcon from '@material-ui/icons/FirstPage';
import KeyboardArrowLeft from '@material-ui/icons/KeyboardArrowLeft';
import KeyboardArrowRight from '@material-ui/icons/KeyboardArrowRight';
import LastPageIcon from '@material-ui/icons/LastPage';
import { makeStyles, useTheme } from '@material-ui/core/styles';
import PropTypes from 'prop-types';
import {
    withRouter,
    Redirect
} from 'react-router-dom';
import {useHistory} from "react-router-dom";
import VisibilityIcon from '@material-ui/icons/Visibility';
import Grid from "@material-ui/core/Grid";
import TextField from '@material-ui/core/TextField';
import {UserContext} from "../../context/UserContext";
import {OktaUserContext} from "../../context/OktaUserContext";
import {entityList} from "../../crud/enitity.crud";



const useStyles1 = makeStyles(theme => ({
    root: {
      flexShrink: 0,
      color: theme.palette.text.secondary,
      marginLeft: theme.spacing(2.5),
    },
  }));

const useStyles2 = makeStyles(theme => ({
    root: {
        width: '100%',
        marginTop: theme.spacing(3),
        overflowX: 'auto',
    },
    table: {
        minWidth: 650,
    },
    container: {
        maxHeight: 340,
      },
}));

const searchEntityList = (key, value) => {
  if(key==="Enter"){
    // code
  }
  
}

function TablePaginationActions(props) {
    const classes = useStyles1();
    const theme = useTheme();
    const { count, page, rowsPerPage, onChangePage } = props;
  
    function handleFirstPageButtonClick(event) {
      onChangePage(event, 0);
    }
  
    function handleBackButtonClick(event) {
      onChangePage(event, page - 1);
    }
  
    function handleNextButtonClick(event) {
      onChangePage(event, page + 1);
    }
  
    function handleLastPageButtonClick(event) {
      onChangePage(event, Math.max(0, Math.ceil(count / rowsPerPage) - 1));
    }
    
  
    return (
      <div className={classes.root}>
        <IconButton
          onClick={handleFirstPageButtonClick}
          disabled={page === 0}
          aria-label="First Page"
        >
          {theme.direction === 'rtl' ? <LastPageIcon /> : <FirstPageIcon />}
        </IconButton>
        <IconButton onClick={handleBackButtonClick} disabled={page === 0} aria-label="Previous Page">
          {theme.direction === 'rtl' ? <KeyboardArrowRight /> : <KeyboardArrowLeft />}
        </IconButton>
        <IconButton
          onClick={handleNextButtonClick}
          disabled={page >= Math.ceil(count / rowsPerPage) - 1}
          aria-label="Next Page"
        >
          {theme.direction === 'rtl' ? <KeyboardArrowLeft /> : <KeyboardArrowRight />}
        </IconButton>
        <IconButton
          onClick={handleLastPageButtonClick}
          disabled={page >= Math.ceil(count / rowsPerPage) - 1}
          aria-label="Last Page"
        >
          {theme.direction === 'rtl' ? <FirstPageIcon /> : <LastPageIcon />}
        </IconButton>
      </div>
    );
  }
  
  TablePaginationActions.propTypes = {
    count: PropTypes.number.isRequired,
    onChangePage: PropTypes.func.isRequired,
    page: PropTypes.number.isRequired,
    rowsPerPage: PropTypes.number.isRequired,
  };


function EntityListing(props) {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const [state, setState] = React.useState('');
    const [page, setPage] = React.useState(0);
    const [rowsPerPage, setRowsPerPage] = React.useState(10);
    const [count, setCount] = React.useState();
    const [entitydata, setEntityData] = React.useState();
    const classes = useStyles2();

    const emptyRows = rowsPerPage - Math.min(rowsPerPage, count - page * rowsPerPage);
    const [loading, setLoading] = React.useState(true);
    const history = useHistory();
    
    async function handleChangePage(event, newPage) {
      console.log(newPage);
        
        setPage(newPage);
        await  fetchData(newPage);

      }
    
    function handleChangeRowsPerPage(event) {
    setRowsPerPage(parseInt(event.target.value, 10));
    }
    useEffect(() => {
            asyncDataFetch();
    }, [])

    const asyncDataFetch = async () => {
            await fetchData();
            setLoading(false);
    }

    const fetchData = async (page = 0 ) => {
        const data = await entityList(page+1).then(response => {
            setEntityData(response.data);
            setCount(response.total); 
        })
    }

    /* const settingData = {
        columns: [
            {title: 'Name', field: 'entity_name'},
            {title: 'Filing State', field: 'entity_state'},
            {title: 'Formation Date', field: 'created_at'},
        ],
        data: entitydata,
    }; */


    return (

        <Grid item xs={12}>
            <Paper className={classes.root}>
                <TableContainer className={classes.container}>


                    <Table 
                     stickyHeader
                     aria-labelledby={props.title !== '' ? props.title : ''}
                     aria-label="sticky table"
                    >
                    <TableHead>
                      <TableRow>
                          <TableCell colSpan={2} component="th" ><h4>Entity List</h4></TableCell>
                          <TableCell colSpan={1} component="th" align="right">
                            <TextField
                              onKeyPress={e => searchEntityList( e.key,e.target.value)}
                              placeholder={'Search'}
                            />
                          </TableCell>
                        </TableRow>
                        <TableRow>
                        <TableCell component="th" >Name</TableCell>
                          <TableCell component="th" >Filing State</TableCell>
                          <TableCell component="th" >Formation Date</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {entitydata && entitydata.map(row => (
                        <TableRow key={row.id}>
                            <TableCell >
                            {row.entity_name}
                            </TableCell>
                            <TableCell >{row.entity_state}</TableCell>
                            <TableCell >{row.created_at}</TableCell>
                        </TableRow>
                        ))}
                    </TableBody>
                    </Table>
                </TableContainer>
                <TablePagination
                            colSpan={3}
                            count={count}
                            rowsPerPage={rowsPerPage}
                            page={page}
                            /* SelectProps={{
                            inputProps: { 'aria-label': 'Rows per page' },
                            native: true,
                            }} */
                            onChangePage={handleChangePage}
                           // onChangeRowsPerPage={handleChangeRowsPerPage}
                             ActionsComponent={TablePaginationActions}
                        />
                </Paper>
        </Grid>

    )
}


export default withRouter(EntityListing);
