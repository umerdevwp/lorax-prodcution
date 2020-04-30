import React, {useContext, useEffect} from 'react';
import Link from '@material-ui/core/Link';
import Table from '@material-ui/core/Table';
import TableHead from '@material-ui/core/TableHead';
import TableContainer from '@material-ui/core/TableContainer';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableFooter from '@material-ui/core/TableFooter';
import TablePagination from '@material-ui/core/TablePagination';
import Pagination from 'react-js-pagination';
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


  
  function createData(name, calories, fat) {
    return { name, calories, fat };
  }
  
  const rows = [
    createData('Cupcake', 305, 3.7),
    createData('Donut', 452, 25.0),
    createData('Eclair', 262, 16.0),
    createData('Frozen yoghurt', 159, 6.0),
    createData('Gingerbread', 356, 16.0),
    createData('Honeycomb', 408, 3.2),
    createData('Ice cream sandwich', 237, 9.0),
    createData('Jelly Bean', 375, 0.0),
    createData('KitKat', 518, 26.0),
    createData('Lollipop', 392, 0.2),
    createData('Marshmallow', 318, 0),
    createData('Nougat', 360, 19.0),
    createData('Oreo', 437, 18.0),
  ].sort((a, b) => (a.calories < b.calories ? -1 : 1));

function EntityListing(props) {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const [state, setState] = React.useState('');
    const [data, setEntityData] = React.useState();
    const [currentPage, setCurrentPage] = React.useState(1);
    const [perPage, setPerPage] = React.useState(10);
    const [total, setTotal] = React.useState(72525);
    const classes = useStyles2();

    const [loading, setLoading] = React.useState(true);
    const history = useHistory();
    
    function handleChangePage() {
       
      }
    
  
    useEffect(() => {
            asyncDataFetch();
    }, [])

    const asyncDataFetch = async () => {
            await fetchData();
            setLoading(false);
    }

    const fetchData = async () => {
        const data = await entityList(currentPage).then(response => {
            setEntityData(response.data);
            setTotal(response.total);
            setCurrentPage(response.current_page);
            setPerPage(response.per_page);
            setTotal(response.total);
            console.log(response);
            console.log(response.total);
            console.log(response.per_page)
            console.log(total);
            console.log(perPage);
            console.log(currentPage);
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
                          <TableCell component="th" >Name</TableCell>
                          <TableCell component="th" >Filing State</TableCell>
                          <TableCell component="th" >Formation Date</TableCell>
                         
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {data && data.map((entity, index) => (
                        <TableRow key={index}>
                            <TableCell  >
                                {entity.entity_name}
                            </TableCell>
                            <TableCell>{entity.entity_state}</TableCell>
                            <TableCell >{entity.created_at}</TableCell>
                        </TableRow>
                        ))}
                    </TableBody>
                    </Table>
                </TableContainer>
                <Pagination
                          activePage={currentPage}
                          totalItemsCount = {total}
                          itemsCountPerPage = {perPage}
                          onChangePage={asyncDataFetch} 
                          itemClass="page-item"
                          linkClass="page-link"
                          firstPageText="First"
                          lastPageText="Last" 
                        />
                </Paper>
        </Grid>

    )
}


export default withRouter(EntityListing);
